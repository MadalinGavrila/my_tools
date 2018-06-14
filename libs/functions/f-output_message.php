<?php

function output_message($message = "") {
    if (!empty($message)) {
        $html = '<div class="message">'. "\n";
        $html .= '<p class="message-text">' . $message . '</p>'. "\n";
        $html .= '</div>'. "\n";
        return $html;
    } else {
        return "";
    }
}