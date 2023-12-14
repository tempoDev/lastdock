<?php
// Hook para verificar y enviar correos electrónicos en el inicio del sitio
add_action('init', 'birthday_reminder_check_birthdays');

function birthday_reminder_check_birthdays() {
    $users = get_users(['meta_key' => 'fecha_de_nacimiento', 'fields' => 'ID']);

    foreach ($users as $user_id) {
        
        // Verifica si el correo ya se ha enviado hoy
        $already_sent = get_transient('birthday_email_sent_' . $user_id);

        if (!$already_sent) {
            
            $birthday = get_user_meta($user_id, 'fecha_de_nacimiento', true);

            
            if (date('m-d') === date('m-d', strtotime($birthday))) {
                
                $user_email = get_userdata($user_id)->user_email;
                $user_info = get_userdata($user_id);
                $user_name = current(explode(' ', get_userdata($user_id)->display_name));

                $archivo_html = file_get_contents(plugin_dir_path(__FILE__) . 'felicitacion.html');

                $subject = '¡Feliz Cumpleaños!';
                $message = str_replace('%USERNAME%', $user_name, $archivo_html);

                $headers = array(
                    'Content-Type: text/html; charset=UTF-8',
                );

                echo("mailing to...");
                
                $sent = wp_mail($user_email, $subject, $message, $headers);

                // LOGGER
                if ($sent) {
                    $log_message = "Correo enviado a usuario ID: $user_id, Email: $user_email, Fecha: " . date('Y-m-d H:i:s');
                    error_log($log_message);
                } else {
                    $log_message = "Error al enviar correo a usuario ID: $user_id, Email: $user_email, Fecha: " . date('Y-m-d H:i:s');
                    error_log($log_message);
                }

                // Establece el transitorio para evitar que se envíe el correo nuevamente hoy
                set_transient('birthday_email_sent_' . $user_id, true, DAY_IN_SECONDS);
            }
        }
    }
}