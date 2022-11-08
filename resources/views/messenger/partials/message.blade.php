<?php $class = '' ?>

<div class="media alert {{ $class }}">
    
    <p>
        <small><strong>Subject:</strong> {{ $message['subject'] }}</small>
    </p>
    <p>
        <small><strong>Message:</strong> {{ $message['message'] }}</small>
    </p>
    <p>
        <small><strong>Sender:</strong> {{ $message['sender'] }}</small>
    </p>
</div>