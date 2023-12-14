<?php

// Hook para guardar la fecha de nacimiento al actualizar el perfil del usuario
add_action('personal_options_update', 'birthday_reminder_save_birthday');
add_action('edit_user_profile_update', 'birthday_reminder_save_birthday');

function birthday_reminder_save_birthday($user_id) {
    if (current_user_can('edit_user', $user_id)) {
        $birthday = $_POST['fecha_de_nacimiento']; 
        update_user_meta($user_id, 'fecha_de_nacimiento', $birthday);
    }
}

/*---------------------------------------------------------------------------------------------------------------------------------- */

// Hook para ejecutar la tarea diariamente
add_action('birthday_reminder_daily_event', 'birthday_reminder_send_birthday_emails');

// Función para enviar correos electrónicos en el día del cumpleaños
function birthday_reminder_send_birthday_emails() {
    
    include_once(plugin_dir_path(__FILE__) . 'email-functions.php');

    birthday_reminder_check_birthdays();
}