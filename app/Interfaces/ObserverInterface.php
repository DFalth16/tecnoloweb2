<?php

namespace App\Interfaces;

interface ObserverInterface
{
    /**
     * Update the observer with new data.
     * 
     * @param mixed $data
     * @return void
     */
    public function update($data);
}
