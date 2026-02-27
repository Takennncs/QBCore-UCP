<?php
require_once 'steamauth/userInfo.php';

if ($_SESSION['whitelisted'] == 1) {
    header("Location: dashboard.php");
    exit;
}

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$stmt = $pdo->prepare("SELECT * FROM whitelist_attempts WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$attempt = $stmt->fetch(PDO::FETCH_ASSOC);

$currentTime = date('Y-m-d H:i:s');
$cooldownActive = false;
$cooldownMinutes = 0;

if ($attempt && $attempt['cooldown_until'] && $attempt['cooldown_until'] > $currentTime) {
    $cooldownActive = true;
    $cooldownUntil = strtotime($attempt['cooldown_until']);
    $cooldownMinutes = ceil(($cooldownUntil - time()) / 60);
}

$questions = [];
$stmt = $pdo->query("SELECT * FROM whitelist_questions WHERE active = 1 ORDER BY order_num ASC");
$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
$totalQuestions = count($questions);

if (!isset($_SESSION['whitelist_answers'])) {
    $_SESSION['whitelist_answers'] = [];
    $_SESSION['whitelist_current'] = 0;
}

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$cooldownActive) {
    if (isset($_POST['answer']) && isset($_SESSION['whitelist_current'])) {
        $currentIndex = $_SESSION['whitelist_current'];
        $selectedAnswer = $_POST['answer'];
        
        if ($currentIndex < $totalQuestions) {
            $currentQuestion = $questions[$currentIndex];
            $isCorrect = ($selectedAnswer === $currentQuestion['correct_answer']);
            
            $_SESSION['whitelist_answers'][$currentIndex] = [
                'question_id' => $currentQuestion['id'],
                'selected' => $selectedAnswer,
                'correct' => $isCorrect,
                'correct_answer' => $currentQuestion['correct_answer']
            ];
            
            $_SESSION['whitelist_current']++;
            
            if ($_SESSION['whitelist_current'] >= $totalQuestions) {
                $result = processWhitelistResults($pdo, $_SESSION['user_id'], $_SESSION['whitelist_answers']);
                if ($result) {
                    header("Location: connect.php?result=1");
                    exit;
                }
            } else {
                header("Location: connect.php");
                exit;
            }
        }
    } elseif (isset($_POST['restart'])) {
        unset($_SESSION['whitelist_answers']);
        unset($_SESSION['whitelist_current']);
        header("Location: connect.php");
        exit;
    }
}

function processWhitelistResults($pdo, $userId, $answers) {
    $correctCount = 0;
    $totalCount = count($answers);
    
    foreach ($answers as $answer) {
        if ($answer['correct']) {
            $correctCount++;
        }
        
        $stmt = $pdo->prepare("INSERT INTO whitelist_answers (user_id, question_id, selected_answer, is_correct, attempt_date) VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute([$userId, $answer['question_id'], $answer['selected'], $answer['correct'] ? 1 : 0]);
    }
    
    $stmt = $pdo->prepare("SELECT * FROM whitelist_attempts WHERE user_id = ?");
    $stmt->execute([$userId]);
    $attempt = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $requiredCorrect = ceil($totalCount * 0.7);
    $passed = ($correctCount >= $requiredCorrect);
    
    if ($attempt) {
        $newAttempts = $attempt['attempts'] + 1;
        
        if (!$passed) {
            $cooldownMinutes = 10 * $newAttempts;
            $cooldownUntil = date('Y-m-d H:i:s', strtotime("+{$cooldownMinutes} minutes"));
            
            $stmt = $pdo->prepare("UPDATE whitelist_attempts SET attempts = ?, last_attempt = NOW(), cooldown_until = ? WHERE user_id = ?");
            $stmt->execute([$newAttempts, $cooldownUntil, $userId]);
        } else {
            $stmt = $pdo->prepare("UPDATE whitelist_attempts SET attempts = ?, last_attempt = NOW(), passed = 1, passed_at = NOW() WHERE user_id = ?");
            $stmt->execute([$newAttempts, $userId]);
        }
    } else {
        if (!$passed) {
            $cooldownMinutes = 10;
            $cooldownUntil = date('Y-m-d H:i:s', strtotime("+{$cooldownMinutes} minutes"));
            
            $stmt = $pdo->prepare("INSERT INTO whitelist_attempts (user_id, attempts, last_attempt, cooldown_until, passed) VALUES (?, 1, NOW(), ?, 0)");
            $stmt->execute([$userId, $cooldownUntil]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO whitelist_attempts (user_id, attempts, last_attempt, passed, passed_at) VALUES (?, 1, NOW(), 1, NOW())");
            $stmt->execute([$userId]);
        }
    }
    
    $_SESSION['whitelist_result'] = [
        'passed' => $passed,
        'correct' => $correctCount,
        'total' => $totalCount,
        'required' => $requiredCorrect
    ];
    
    unset($_SESSION['whitelist_answers']);
    unset($_SESSION['whitelist_current']);
    
    if ($passed) {
        $stmt = $pdo->prepare("UPDATE ucp_users SET whitelisted = 1 WHERE id = ?");
        $stmt->execute([$userId]);
        $_SESSION['whitelisted'] = 1;
    }
    
    return true;
}

$showResult = isset($_GET['result']) && isset($_SESSION['whitelist_result']);
if ($showResult) {
    $result = $_SESSION['whitelist_result'];
}
?>

<!DOCTYPE html>
<html lang="et">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Whitelist</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/connect.css">
</head>
<body>
    <div class="hero-bg"></div>
    <div class="overlay"></div>

    <div class="container">
        <div class="progress-steps">
            <div class="steps-row">
                <div class="step-item">
                    <div class="step-circle completed">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                            <path d="M20 6L9 17L4 12" stroke="currentColor" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <span class="step-label">Steam login</span>
                </div>
                <div class="step-line"></div>
                <div class="step-item">
                    <div class="step-circle <?= $showResult ? ($result['passed'] ? 'completed' : 'active') : (isset($_SESSION['whitelist_current']) && $_SESSION['whitelist_current'] > 0 ? 'active' : 'pending') ?>">
                        <?php if ($showResult && $result['passed']): ?>
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                                <path d="M20 6L9 17L4 12" stroke="currentColor" stroke-linecap="round"/>
                            </svg>
                        <?php else: ?>
                            2
                        <?php endif; ?>
                    </div>
                    <span class="step-label">Whitelist</span>
                </div>
                <div class="step-line"></div>
                <div class="step-item">
                    <div class="step-circle <?= ($showResult && $result['passed']) || $_SESSION['whitelisted'] == 1 ? 'completed' : 'pending' ?>">
                        <?php if (($_SESSION['whitelisted'] == 1)): ?>
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                                <path d="M20 6L9 17L4 12" stroke="currentColor" stroke-linecap="round"/>
                            </svg>
                        <?php else: ?>
                            3
                        <?php endif; ?>
                    </div>
                    <span class="step-label">Avalehele</span>
                </div>
            </div>
        </div>

        <?php if ($cooldownActive): ?>
            <div class="question-card">
                <div class="message warning">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="12" y1="8" x2="12" y2="12"/>
                        <circle cx="12" cy="16" r="1" fill="currentColor"/>
                    </svg>
                    Sa ei saanud whitelistga hakkama
                </div>
                <div class="cooldown-timer">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#fbbf24" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <polyline points="12 6 12 12 16 14"/>
                    </svg>
                    <div class="timer" id="cooldown-timer"><?= $cooldownMinutes ?>:00</div>
                    <p>Järgmine katse on võimalik pärast cooldowni lõppu</p>
                    <p style="font-size:0.85rem; color:#94a3b8; margin-top:0.5rem;">
                        Iga ebaõnnestunud katse pikendab cooldowni (10min → 20min → 30min...)
                    </p>
                </div>
            </div>
        <?php elseif ($showResult): ?>
            <div class="question-card result-card">
                <?php if ($result['passed']): ?>
                    <div class="result-icon success">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                            <polyline points="22 4 12 14.01 9 11.01"/>
                        </svg>
                    </div>
                    <h2 style="font-size:1.8rem; margin-bottom:0.5rem;">Palju õnne!</h2>
                    <p class="score-detail">Sa said whitelisti läbi</p>
                    <div class="score"><?= $result['correct'] ?>/<?= $result['total'] ?></div>
                    <p style="color:#94a3b8; margin-bottom:2rem;">
                        Nõutav oli vähemalt <?= $result['required'] ?> õiget vastust (70%)
                    </p>
                    <a href="dashboard.php" class="restart-btn">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 12h4l3-9 4 18 3-9h4"/>
                        </svg>
                        Mine dashboardi
                    </a>
                <?php else: ?>
                    <div class="result-icon failure">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="18" y1="6" x2="6" y2="18"/>
                            <line x1="6" y1="6" x2="18" y2="18"/>
                        </svg>
                    </div>
                    <h2 style="font-size:1.8rem; margin-bottom:0.5rem;">Kahjuks ei läbinud</h2>
                    <p class="score-detail">Sa said <?= $result['correct'] ?>/<?= $result['total'] ?> õiget</p>
                    <div class="score"><?= round(($result['correct']/$result['total'])*100) ?>%</div>
                    <p style="color:#94a3b8; margin-bottom:1rem;">
                        Nõutav oli vähemalt <?= $result['required'] ?> õiget vastust (70%)
                    </p>
                    <p style="color:#fbbf24; margin-bottom:2rem;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:inline; margin-right:0.3rem;">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="12" y1="8" x2="12" y2="12"/>
                            <line x1="12" y1="16" x2="12.01" y2="16"/>
                        </svg>
                        Järgmine katse on võimalik 10 minuti pärast
                    </p>
                    <form method="POST">
                        <button type="submit" name="restart" class="restart-btn">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M23 4v6h-6"/>
                                <path d="M1 20v-6h6"/>
                                <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/>
                            </svg>
                            Proovi uuesti
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        <?php elseif (empty($questions)): ?>
            <div class="question-card">
                <div class="message error">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="12" y1="8" x2="12" y2="12"/>
                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    Küsimusi pole veel lisatud. Palun kontakteeru adminiga.
                </div>
            </div>
        <?php else: ?>
            <?php
            $currentIndex = $_SESSION['whitelist_current'] ?? 0;
            if ($currentIndex >= $totalQuestions) {
                $currentIndex = $totalQuestions - 1;
                $_SESSION['whitelist_current'] = $totalQuestions - 1;
            }
            $currentQuestion = $questions[$currentIndex];
            $progress = (($currentIndex + 1) / $totalQuestions) * 100;
            $selectedAnswer = $_SESSION['whitelist_answers'][$currentIndex]['selected'] ?? null;
            ?>

            <div class="question-card">
                <div class="progress-header">
                    <span class="progress-text">Küsimus <?= $currentIndex + 1 ?> / <?= $totalQuestions ?></span>
                    <a href="rules.php" target="_blank" class="rules-link">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <polyline points="14 2 14 8 20 8"/>
                            <line x1="16" y1="13" x2="8" y2="13"/>
                            <line x1="16" y1="17" x2="8" y2="17"/>
                            <polyline points="10 9 9 9 8 9"/>
                        </svg>
                        Vaata reegleid
                    </a>
                </div>

                <div class="progress-bar">
                    <div class="progress-fill" style="width: <?= $progress ?>%"></div>
                </div>

                <div class="question-text">
                    <?= htmlspecialchars($currentQuestion['question']) ?>
                </div>

                <form method="POST" id="questionForm">
                    <div class="options-grid">
                        <?php
                        $options = [
                            'A' => $currentQuestion['option_a'],
                            'B' => $currentQuestion['option_b'],
                            'C' => $currentQuestion['option_c'],
                            'D' => $currentQuestion['option_d']
                        ];
                        
                        foreach ($options as $letter => $text):
                            $isSelected = ($selectedAnswer === $letter);
                        ?>
                            <button type="button" class="option-btn <?= $isSelected ? 'selected' : '' ?>" onclick="selectAnswer('<?= $letter ?>', <?= $currentIndex ?>)">
                                <span class="option-letter"><?= $letter ?></span>
                                <?= htmlspecialchars($text) ?>
                            </button>
                        <?php endforeach; ?>
                    </div>

                    <input type="hidden" name="answer" id="selectedAnswer" value="<?= $selectedAnswer ?? '' ?>">

                    <div class="nav-buttons">
                        <button type="button" class="nav-btn back" onclick="goToPreviousQuestion()" <?= $currentIndex === 0 ? 'disabled' : '' ?>>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M19 12H5M12 19l-7-7 7-7"/>
                            </svg>
                            Tagasi
                        </button>
                        <button type="submit" class="nav-btn next" id="nextBtn" <?= !$selectedAnswer ? 'disabled' : '' ?>>
                            <?= $currentIndex === $totalQuestions - 1 ? 'Esita' : 'Järgmine' ?>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M5 12h14M12 5l7 7-7 7"/>
                            </svg>
                        </button>
                    </div>
                </form>

                <div class="dots-container">
                    <?php for ($i = 0; $i < $totalQuestions; $i++): ?>
                        <div class="dot 
                            <?= isset($_SESSION['whitelist_answers'][$i]) ? 'completed' : '' ?>
                            <?= $i === $currentIndex ? 'active' : '' ?>
                        "></div>
                    <?php endfor; ?>
                </div>
            </div>
        <?php endif; ?>

        <footer>
            <p>All rights reserved © 2026</p>
            <p style="margin-top:0.25rem;">Made by takenncs</p>
        </footer>
    </div>

    <script>
    function selectAnswer(letter, index) {
        document.getElementById('selectedAnswer').value = letter;
        
        document.querySelectorAll('.option-btn').forEach(btn => {
            btn.classList.remove('selected');
        });
        
        event.currentTarget.classList.add('selected');
        
        document.getElementById('nextBtn').disabled = false;
    }

    function goToPreviousQuestion() {
        window.location.href = 'whitelist_prev.php';
    }

    <?php if ($cooldownActive): ?>
    let minutes = <?= $cooldownMinutes ?>;
    let seconds = 0;
    
    function updateTimer() {
        const timerElement = document.getElementById('cooldown-timer');
        if (timerElement) {
            if (seconds === 0) {
                if (minutes === 0) {
                    location.reload();
                    return;
                }
                minutes--;
                seconds = 59;
            } else {
                seconds--;
            }
            
            timerElement.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
        }
    }
    
    setInterval(updateTimer, 1000);
    <?php endif; ?>
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
</body>
</html>