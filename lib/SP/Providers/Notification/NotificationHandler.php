<?php
/*
 * sysPass
 *
 * @author nuxsmin
 * @link https://syspass.org
 * @copyright 2012-2022, Rubén Domínguez nuxsmin@$syspass.org
 *
 * This file is part of sysPass.
 *
 * sysPass is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * sysPass is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with sysPass.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace SP\Providers\Notification;

use Exception;
use SP\Core\Application;
use SP\Core\Events\Event;
use SP\Core\Events\EventReceiver;
use SP\DataModel\NotificationData;
use SP\Domain\Notification\Ports\NotificationServiceInterface;
use SP\Domain\Notification\Services\NotificationService;
use SP\Providers\EventsTrait;
use SP\Providers\Provider;
use SplSubject;

/**
 * Class NotificationHandler
 *
 * @package SP\Providers\Notification
 */
final class NotificationHandler extends Provider implements EventReceiver
{
    use EventsTrait;

    public const EVENTS = [
        'request.account',
        'show.account.link',
    ];

    private NotificationService $notificationService;
    private string              $events;

    public function __construct(
        Application $application,
        NotificationServiceInterface $notificationService
    ) {
        $this->notificationService = $notificationService;

        parent::__construct($application);
    }

    /**
     * Devuelve los eventos que implementa el observador
     *
     * @return array
     */
    public function getEvents(): array
    {
        return self::EVENTS;
    }

    /**
     * Devuelve los eventos que implementa el observador en formato cadena
     *
     * @return string
     */
    public function getEventsString(): string
    {
        return $this->events;
    }

    /**
     * Receive update from subject
     *
     * @link  http://php.net/manual/en/splobserver.update.php
     *
     * @param  SplSubject  $subject  <p>
     *                            The <b>SplSubject</b> notifying the observer of an update.
     *                            </p>
     *
     * @return void
     * @since 5.1.0
     */
    public function update(SplSubject $subject): void
    {
        $this->updateEvent('update', new Event($subject));
    }

    /**
     * Evento de actualización
     *
     * @param  string  $eventType  Nombre del evento
     * @param  Event  $event  Objeto del evento
     */
    public function updateEvent(string $eventType, Event $event): void
    {
        switch ($eventType) {
            case 'request.account':
                $this->requestAccountNotification($event);
                break;
            case 'show.account.link':
                $this->showAccountLinkNotification($event);
                break;
        }
    }

    /**
     * @param  Event  $event
     */
    private function requestAccountNotification(Event $event): void
    {
        $eventMessage = $event->getEventMessage();
        $data = $eventMessage !== null ? $eventMessage->getExtra() : [];

        foreach ($data['userId'] as $userId) {
            $notificationData = new NotificationData();
            $notificationData->setType(__('Request'));
            $notificationData->setComponent(__('Accounts'));
            $notificationData->setUserId($userId);
            $notificationData->setDescription($eventMessage, true);

            $this->notify($notificationData);
        }
    }

    /**
     * @param  NotificationData  $notificationData
     */
    private function notify(NotificationData $notificationData): void
    {
        try {
            $this->notificationService->create($notificationData);
        } catch (Exception $e) {
            processException($e);
        }
    }

    /**
     * @param  Event  $event
     */
    private function showAccountLinkNotification(Event $event): void
    {
        $eventMessage = $event->getEventMessage();
        $data = $eventMessage !== null ? $eventMessage->getExtra() : [];

        if ($data['notify'][0] === true) {
            $notificationData = new NotificationData();
            $notificationData->setType(__('Notification'));
            $notificationData->setComponent(__('Accounts'));
            $notificationData->setUserId($data['userId'][0]);
            $notificationData->setDescription($eventMessage, true);

            $this->notify($notificationData);
        }
    }

    public function initialize(): void
    {
        $this->events = $this->parseEventsToRegex(self::EVENTS);
        $this->initialized = true;
    }
}
