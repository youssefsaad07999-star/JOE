<?php

namespace App;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum PaymentStatus: string implements HasColor, HasIcon, HasLabel
{
    case Pending = 'pending';
    case Paid = 'paid';
    case Failed = 'failed';
    case Refunded = 'refunded';
    case Cancelled = 'cancelled';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Paid => 'Paid',
            self::Failed => 'Failed',
            self::Refunded => 'Refunded',
            self::Cancelled => 'Cancelled',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Pending => 'warning', // Yellow
            self::Paid => 'success', // Green
            self::Failed => 'danger',  // Red
            self::Refunded => 'gray',    // Gray
            self::Cancelled => 'danger',  // Red
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Pending => 'heroicon-o-clock',
            self::Paid => 'heroicon-o-check-circle',
            self::Failed => 'heroicon-o-x-circle',
            self::Refunded => 'heroicon-o-arrow-uturn-left',
            self::Cancelled => 'heroicon-o-minus-circle',
        };
    }

    /**
     * Map raw Paddle string statuses safely into your clean enum cases.
     */
    public static function fromPaddle(string $status): self
    {
        return match (strtolower($status)) {
            'paid', 'completed' => self::Paid,
            'failed', 'payment_failed' => self::Failed,
            'refunded' => self::Refunded,
            'canceled', 'cancelled' => self::Cancelled,
            default => self::Pending,
        };
    }
}
