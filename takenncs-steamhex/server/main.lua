local QBCore = exports['qb-core']:GetCoreObject()

local function AddSteamHex(playerId)
    if not playerId or playerId == 0 then
        return
    end
    
    local identifiers = GetPlayerIdentifiers(playerId)
    local steamHex = nil
    
    for _, id in ipairs(identifiers) do
        if string.find(id, "steam:") then
            steamHex = id
            break
        end
    end
    
    if not steamHex then
        return false
    end
    
    local xPlayer = QBCore.Functions.GetPlayer(playerId)
    if not xPlayer or not xPlayer.PlayerData or not xPlayer.PlayerData.citizenid then
        return false
    end
    
    local citizenid = xPlayer.PlayerData.citizenid
    
    exports['oxmysql']:update('UPDATE players SET steamhex = ? WHERE citizenid = ?', 
        {steamHex, citizenid},
        function(affectedRows)
            if affectedRows and affectedRows > 0 then
                xPlayer.PlayerData.steamhex = steamHex
            else
                local license = nil
                for _, id in ipairs(identifiers) do
                    if string.find(id, "license:") then
                        license = id
                        break
                    end
                end
                
                if license then
                    exports['oxmysql']:update('UPDATE players SET steamhex = ? WHERE license = ?', 
                        {steamHex, license}
                    )
                end
            end
        end
    )
    
    return true
end

AddEventHandler('playerJoining', function()
    local src = source
    
    Citizen.SetTimeout(3000, function()
        local success = AddSteamHex(src)
        
        if not success then
            local retries = 0
            local maxRetries = 10
            
            local retryHandle = setInterval(function()
                retries = retries + 1
                
                local retrySuccess = AddSteamHex(src)
                
                if retrySuccess or retries >= maxRetries then
                    clearInterval(retryHandle)
                end
            end, 2000)
        end
    end)
end)

RegisterNetEvent('QBCore:Server:PlayerLoaded', function(PlayerData)
    local src = source
    
    if src and src > 0 then
        Citizen.SetTimeout(2000, function()
            AddSteamHex(src)
        end)
    end
end)

RegisterNetEvent('QBCore:Server:OnPlayerLoaded', function()
    local src = source
    if src and src > 0 then
        Citizen.SetTimeout(2000, function()
            AddSteamHex(src)
        end)
    end
end)