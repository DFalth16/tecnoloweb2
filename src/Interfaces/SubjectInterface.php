<?php

namespace EventCore\Interfaces;

interface SubjectInterface
{
    public function attach(ObserverInterface $observer);
    public function detach(ObserverInterface $observer);
    public function notify($data);
}
