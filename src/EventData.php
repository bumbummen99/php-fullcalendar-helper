<?php

namespace App\Calendar;

use DateTimeInterface;
use InvalidArgumentException;
use JsonSerializable;
use Stringable;

/**
 * This class implements the data required to 
 * represent an event for the FullCalendar 
 * JavaScript frontend library.
 * 
 * This class is designed in a framework
 * agnostic manner so that it can be
 * reused in any project.
 * 
 * It hs been inspired by the Laravel FullCalendar 
 * and FilamentPHP FullCalendar packages.
 * 
 * @see https://github.com/maddhatter/laravel-fullcalendar
 * @see https://github.com/saade/filament-fullcalendar
 * 
 * @author Patrick Henninger <privat@skyraptor.eu>
 * @license MIT
 * 
 */
class EventData implements JsonSerializable, Stringable
{
    public function __construct(
        /**
         * The unique identifier of the event.
         */
        public readonly int|string $id,

        /**
         * The title of the event.
         */
        public readonly string $title,

        /**
         * Determines if the event is all day.
         */
        public readonly bool $allDay = false,

        /**
         * The start date time of the evebt.
         */
        public readonly DateTimeInterface $start,

        /**
         * The end date time of the evebt.
         */
        public readonly ?DateTimeInterface $end = null,

        /**
         * The identifiers of the resources this Event is associated to.
         */
        public readonly array $resourceIds = [],

        /**
         * The identifier of the Group this Event is associated to.
         */
        public readonly int|string|null $groupId = null,

        /**
         * The URL to redirect the user to when the event is clicked.
         */
        public readonly ?string $url = null,

        /**
         * Determines if the URL should be opened in a new tab.
         */
        public readonly bool $openURLInNewTab = false,

        /**
         * The text color of the event.
         */
        public readonly ?string $textColor = null,

        /**
         * The background color of the event.
         */
        public readonly ?string $backgroundColor = null,

        /**
         * The border color of the event.
         */
        public readonly ?string $borderColor = null,

        /**
         * The extendedProps of the event.
         */
        public readonly ?array $extendedProps = null,

        /**
         * Extra options for the serialization.
         */
        public readonly array $options = []
    ) {
        if ($this->allDay && ! is_null($this->end)) {
            throw new InvalidArgumentException(
                'An all day event cannot define an end. Set allDay to false or end to null.'
            );
        }
    }

    /**
     * Automatically use the JSON representation when
     * this instance is used in a string context.
     */
    public function __toString(): string
    {
        return json_encode($this->jsonSerialize());
    }

    /**
     * Provide the data when this instance is provided
     * to the json_encode function.
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * Formats this instance as an array.
     */
    public function toArray(): array
    {
        /* Format the base data */
        $data = [
            'id'          => $this->id,
            'title'       => $this->title,
            'allDay'      => $this->allDay,
            'start'       => $this->start->format(DateTimeInterface::ATOM),
        ];

        /* Append the end date time if it is defined */
        if (! is_null($this->end)) {
            $data['end'] = $this->end->format(DateTimeInterface::ATOM);
        }

        /* Append the resourceId(s) if it is defined */
        if (($count = count($this->resourceIds)) && $count > 0) {
            if ($count === 1) {
                $data['resourceId'] = $this->resourceIds[0];
            } else {
                $data['resourceIds'] = $this->resourceIds;
            }
        }

        /* Append the groupId if it is defined */
        if (! is_null($this->groupId)) {
            $data['groupId'] = $this->groupId;
        }

        /* Append the URL if it is defined */
        if (! is_null($this->url)) {
            $data['url'] = $this->url;
            $data['shouldOpenUrlInNewTab'] = $this->openURLInNewTab;
        }

        /* Apppend the extended props if it is defined */
        if (! is_null($this->extendedProps)) {
            $data['extendedProps'] = $this->extendedProps;
        }

        /* Append the text color if it is defined */
        if (! is_null($this->textColor)) {
            $data['textColor'] = $this->textColor;
        }

        /* Append the background color if it is defined */
        if (! is_null($this->backgroundColor)) {
            $data['backgroundColor'] = $this->backgroundColor;
        }

        /* Append the border color if it is defined */
        if (! is_null($this->borderColor)) {
            $data['borderColor'] = $this->borderColor;
        }

        /* Append any additional options */
        return [
            ...$data,
            ...$this->options
        ];
    }
}
