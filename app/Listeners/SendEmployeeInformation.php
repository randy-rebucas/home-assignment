<?php

namespace App\Listeners;

use App\Events\EmployeeCreated;
use App\Mail\EmployeeInformation;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendEmployeeInformation
{

        /**
     * The employee instance.
     *
     * @var \App\Models\Employee
     */
    public $employee;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(EmployeeCreated $event): void
    {
        $employee = $event->employee;

        // send information email with the employee details
        Mail::to($employee->user->email)->send(new EmployeeInformation($employee));
    }
}
