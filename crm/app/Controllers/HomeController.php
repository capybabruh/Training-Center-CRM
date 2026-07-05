<?php
// app/Controllers/HomeController.php

class HomeController
{
    public function index(): void
    {
        if (is_logged_in()) redirect('/dashboard');
        redirect('/login');
    }
}
