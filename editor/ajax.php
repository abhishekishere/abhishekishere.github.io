<?php $contact_email = "itnova.fl@gmail.com"; $recaptcha_privatekey = "6LfUEfUSAAAAACIkjRGkiGQZQyQUhIfN5nO3a69o"; $recaptcha_publickey = "6LfUEfUSAAAAACuW49vI_JBZyuoWaibNtvTtGhOL"; ?>
<?php
switch ($_POST['action']) {
    case 'get_elements':
        $elements = array();
        $src = '../elements';
        if (is_dir($src)) {
            $js = "var azexo_template_elements = [];\n";
            $dir = opendir($src);
            while (false !== ( $file = readdir($dir))) {
                if (( $file != '.' ) && ( $file != '..' )) {
                    if (!is_dir($src . '/' . $file)) {
                        $info = pathinfo($file);
                        if (empty($info['extension'])) {
                            $elements[$file] = file_get_contents($src . '/' . $file);
                            $js .= 'azexo_template_elements["' . $file . '"]="' . base64_encode($elements[$file]) . '";' . "\n";
                        }
                    }
                }
            }
            closedir($dir);
        } else {
            mkdir($src);
        }
        file_put_contents($src . '/elements.js', $js);
        print json_encode($elements);
        break;
    case 'get_recaptcha_publickey':
        print $recaptcha_publickey;
        break;
    case 'send_email':
        include_once 'recaptcha/recaptchalib.php';
        $resp = recaptcha_check_answer($recaptcha_privatekey, $_SERVER["REMOTE_ADDR"], $_POST["recaptcha_challenge_field"], $_POST["recaptcha_response_field"]);
        mail($contact_email, 'Message from ' . $_POST["name"] . '(' . $_POST["email"] . ')', $_POST["message"]);
        print json_encode($resp);
        break;
    default:
        break;
}
?>