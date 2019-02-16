<?php

class Vars {

    private $from;
    private $to_order;
    private $add;
    private $sendmail_args;
    private $delivery_methods;
    private $payment_methods;


    public function __construct ()
    {
        $this->from = "info@jda.kiev.ua";
        //$this->to_order = "jda@jda.kiev.ua";
        $this->to_order = "jda@jda.kiev.ua, marykos@ukr.net";
        $this->add = "MIME-Version: 1.0\nContent-Type: text/plain; charset=utf-8\nContent-Transfer-Encoding: 8bit";
        $this->sendmail_args = "-finfo@jda.kiev.ua";
        $this->delivery_methods = array(0=>"Не указан", 1=>"Курьер", 2=>"Самовывоз", 3=>"Нова Пошта");
        $this->payment_methods = array(0=>"Не указан", 1=>"Курьеру при получении", 2=>"По согласованию с администрацией");

    }

    public function __get($attr)
    {
        if (isset($this->$attr))
            return $this->$attr;
        else
            die("Атрибут ".$attr." не найден!");
    }


    public function __set($attr, $value)
    {
        $this->$attr = $value;
    }
} 