<?php

namespace Tests\Unit;

use App\Models\User;
use App\Notifications\TestNotification;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class MailNotificationTest extends TestCase
{
    /**
     * @var Collection|Model|mixed
     */
    private mixed $user;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testDatabaseNotification()
    {
        Notification::fake();
        $this->user = User::factory()->create();
        $email_subject = "Test Notification";

        $this->user->notify(new TestNotification($email_subject));

        Notification::assertSentTo($this->user, TestNotification::class, function ($notification, $channels) use ($email_subject) {
            $this->assertContains('database', $channels);

            return true;
        });
    }

    public function testMailNotification()
    {
        Notification::fake();
        $this->user = User::factory()->create();
        $email_subject = "Test notification";

        $this->user->notify(new TestNotification($email_subject));

        Notification::assertSentTo($this->user, TestNotification::class, function ($notification, $channels) use ($email_subject) {
            $this->assertContains('mail', $channels);

            $mailNotification = (object)$notification->toMail($this->user);


            $this->assertEquals('The introduction to the notification.', $mailNotification->introLines[0]);
            $this->assertEquals('Thank you for using our application!', $mailNotification->outroLines[0]);
            $this->assertEquals('Notification Action', $mailNotification->actionText);
            $this->assertEquals($mailNotification->actionUrl, url('/'));

            return true;
        });
    }
}
