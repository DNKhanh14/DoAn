<?php

namespace App\Controllers;

use App\Core\Controller;
use Exception;

class ContactController extends Controller
{
    public function send(): void
    {
        if (
            !isset(
                $_POST['contact_name'],
                $_POST['contact_email'],
                $_POST['contact_subject'],
                $_POST['contact_message']
            )
        ) {
            http_response_code(400);
            echo "<div class='alert alert-danger'>Invalid request.</div>";
            return;
        }

        $contactName = test_input($_POST['contact_name']);
        $contactEmail = test_input($_POST['contact_email']);
        $contactSubject = test_input($_POST['contact_subject']);
        $contactMessage = test_input($_POST['contact_message']);

        try {
            mail('your email', $contactSubject, $contactMessage);
            echo "<div class='alert alert-success'>";
            echo ' The message has been sent successfully';
            echo '</div>';
        } catch (Exception $ex) {
            echo "<div class='alert alert-warning'>";
            echo ' A problem occurred while trying to send the message, please try again!';
            echo '</div>';
        }
    }
}
