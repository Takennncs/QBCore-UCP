<?php
class LightOpenID {
    public $returnUrl, $realm, $required = [], $optional = [];
    private $identity, $trustRoot, $mode;

    function __construct($host = null) {
        if (!isset($host)) {
            $this->trustRoot = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http')
                . '://' . $_SERVER['HTTP_HOST'];
        } else {
            $this->trustRoot = $host;
        }
    }

    public function setIdentity($url) {
        $this->identity = $url;
    }

    public function getIdentity() {
        return isset($_GET['openid_claimed_id']) ? $_GET['openid_claimed_id'] : $this->identity;
    }

    public function setReturnUrl($url) {
        $this->returnUrl = $url;
    }

    public function setRealm($realm) {
        $this->realm = $realm;
    }

    public function getMode() {
        return isset($_GET['openid_mode']) ? $_GET['openid_mode'] : null;
    }

    function authUrl() {
        $params = [
            'openid.ns' => 'http://specs.openid.net/auth/2.0',
            'openid.mode' => 'checkid_setup',
            'openid.return_to' => $this->returnUrl,
            'openid.realm' => $this->trustRoot,
            'openid.identity' => 'http://specs.openid.net/auth/2.0/identifier_select',
            'openid.claimed_id' => 'http://specs.openid.net/auth/2.0/identifier_select'
        ];
        return 'https://steamcommunity.com/openid/login?' . http_build_query($params);
    }

    function validate() {
        if (empty($_GET['openid_claimed_id'])) return false;
        $id = $_GET['openid_claimed_id'];
        if (preg_match('/^https:\\/\\/steamcommunity\\.com\\/openid\\/id\\/(7[0-9]{15,25}+)$/', $id, $matches)) {
            return true;
        }
        return false;
    }
}
?>
