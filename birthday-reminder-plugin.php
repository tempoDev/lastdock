<?php
/*
Plugin Name: Birthday Reminder Plugin
Description: Un plugin para recordar el cumpleaños de los usuarios
Version: 1.0
*/

// ACTIVACIÓN
register_activation_hook(__FILE__, 'birthday_reminder_activate');

function birthday_reminder_activate() {
    $users = get_users(['meta_key' => 'fecha_de_nacimiento', 'fields' => 'ID']);
    foreach ($users as $user_id) {
        delete_transient('birthday_email_sent_' . $user_id);
    }
    
    // Programa la tarea para ejecutarse diariamente a la hora indicada
    wp_schedule_event(strtotime('today 16:59 Europe/Madrid'), 'daily', 'birthday_reminder_daily_event');
}

//-------------------------------------------------------------------------

// DESACTIVACIÓN
register_deactivation_hook(__FILE__, 'birthday_reminder_deactivate');


function birthday_reminder_deactivate() {
    wp_clear_scheduled_hook('birthday_reminder_daily_event');
}


//-------------------------------------------------------------------------

// Archivo con funciones relacionadas con ACF
include_once(plugin_dir_path(__FILE__) . 'acf-functions.php');

// Archivo con funciones del mail
include_once(plugin_dir_path(__FILE__) . 'email-functions.php');