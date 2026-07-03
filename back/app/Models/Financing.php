<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Financing extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'application_id',
        'plan',
        'vehicle_id',

        'user_id',
        'responsable_id',

        'type',
        'installments',
        'start_date',
        'observation',
        'plate',
        'status',
        'payment_initial',

        'months',

        'deuda_adquirida',

        'cost_price',
        'financing_price',
        'interes_price',
        'final_price',

        'price_diario',
        'price_semanal',
        'price_quincenal',
        'price_mensual',

        'moraStatus',
        'interes_porcent',
        'total_inicial',

        'lote_id',
        'last_whatsapp_sent',
        'turned_off_at'
    ];

    protected $casts = [
        'turned_off_at' => 'datetime',
    ];

    // Relaciones
    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function responsable()
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'financing_id');
    }

    public function lote()
    {
        return $this->belongsTo(Lote::class, 'lote_id');
    }

    public function moraRecords()
    {
        return $this->hasMany(MoraRecord::class, 'financing_id');
    }

    /**
     * The services that belong to the financing.
     */
    public function services()
    {
        return $this->belongsToMany(Service::class)
            ->withPivot('price')
            ->withTimestamps();
    }

    /**
     * Get the due date for the N-th installment based on the plan and start_date.
     */
    public function getNthDueDate(int $n): \Carbon\Carbon
    {
        $dueDate = \Carbon\Carbon::parse($this->start_date);

        if ($this->plan === 'Diario') {
            return $dueDate->copy()->addDays($n);
        }

        if ($this->plan === 'Semanal') {
            // First installment is the first Saturday strictly AFTER start_date
            if ($dueDate->dayOfWeek !== \Carbon\Carbon::SATURDAY) {
                $dueDate->next(\Carbon\Carbon::SATURDAY);
            } else {
                $dueDate->addWeek();
            }
            return $dueDate->addWeeks($n - 1);
        }

        if ($this->plan === 'Quincenal') {
            for ($i = 0; $i < $n; $i++) {
                $endOfMonthLimit = ($dueDate->month === 2) ? $dueDate->copy()->endOfMonth()->day : 30;

                if ($dueDate->day < 15) {
                    $dueDate->day(15);
                } elseif ($dueDate->day < $endOfMonthLimit) {
                    $dueDate->day($endOfMonthLimit);
                } else {
                    $dueDate->addMonthNoOverflow()->day(15);
                }
            }
            return $dueDate;
        }

        if ($this->plan === 'Mensual') {
            for ($i = 0; $i < $n; $i++) {
                $endOfMonthLimit = ($dueDate->month === 2) ? $dueDate->copy()->endOfMonth()->day : 30;

                if ($dueDate->day < $endOfMonthLimit) {
                    $dueDate->day($endOfMonthLimit);
                } else {
                    $dueDate->addMonthNoOverflow();
                    // Adjust target day for the new month
                    $targetDay = ($dueDate->month === 2) ? $dueDate->copy()->endOfMonth()->day : 30;
                    $dueDate->day($targetDay);
                }
            }
            return $dueDate;
        }

        return $dueDate->copy()->addMonths($n);
    }

    /**
     * Get the next logical due date based on the current calendar (Idea 2).
     */
    public function getNextCalendarDueDate(): \Carbon\Carbon
    {
        $lastPayment = $this->payments()
            ->where('status', 'approved')
            ->where('installment_number', '>', 0)
            ->latest('created_at')
            ->first();

        $now = $lastPayment ? \Carbon\Carbon::parse($lastPayment->created_at) : \Carbon\Carbon::now();

        if ($this->plan === 'Diario') {
            // Si hay pago, la siguiente es mañana. Si no hay, es hoy.
            return $lastPayment ? $now->copy()->addDay()->startOfDay() : $now->copy()->startOfDay();
        }

        if ($this->plan === 'Semanal') {
            // Si hay pago hoy (Sábado), queremos el PRÓXIMO Sábado.
            // next() de Carbon siempre busca el estrictamente siguiente si ya es el día.
            if ($lastPayment) {
                return $now->copy()->next(\Carbon\Carbon::SATURDAY)->startOfDay();
            }
            
            if ($now->dayOfWeek === \Carbon\Carbon::SATURDAY) {
                return $now->copy()->startOfDay();
            }
            return $now->copy()->next(\Carbon\Carbon::SATURDAY)->startOfDay();
        }

        if ($this->plan === 'Quincenal') {
            $day = $now->day;
            $endOfMonthLimit = ($now->month === 2) ? $now->copy()->endOfMonth()->day : 30;

            // Si ya pagó la cuota de este periodo (es decir, el $now es la fecha del pago)
            // queremos que avance al siguiente hito.
            if ($lastPayment) {
                if ($day < 15) {
                    return $now->copy()->day(15)->startOfDay();
                } elseif ($day < $endOfMonthLimit) {
                    return $now->copy()->day($endOfMonthLimit)->startOfDay();
                } else {
                    return $now->copy()->addMonthNoOverflow()->day(15)->startOfDay();
                }
            }

            if ($day <= 15) {
                return $now->copy()->day(15)->startOfDay();
            } elseif ($day <= $endOfMonthLimit) {
                return $now->copy()->day($endOfMonthLimit)->startOfDay();
            } else {
                return $now->copy()->addMonthNoOverflow()->day(15)->startOfDay();
            }
        }

        if ($this->plan === 'Mensual') {
            $endOfMonthLimit = ($now->month === 2) ? $now->copy()->endOfMonth()->day : 30;
            
            if ($lastPayment) {
                if ($now->day < $endOfMonthLimit) {
                    return $now->copy()->day($endOfMonthLimit)->startOfDay();
                } else {
                    $nextMonth = $now->copy()->addMonthNoOverflow();
                    $nextLimit = ($nextMonth->month === 2) ? $nextMonth->copy()->endOfMonth()->day : 30;
                    return $nextMonth->day($nextLimit)->startOfDay();
                }
            }

            if ($now->day <= $endOfMonthLimit) {
                return $now->copy()->day($endOfMonthLimit)->startOfDay();
            } else {
                $nextMonth = $now->copy()->addMonthNoOverflow();
                $nextLimit = ($nextMonth->month === 2) ? $nextMonth->copy()->endOfMonth()->day : 30;
                return $nextMonth->day($nextLimit)->startOfDay();
            }
        }

        return $now->copy()->startOfDay();
    }

    /**
     * Get the current installment price based on the plan.
     */
    public function getCurrentInstallmentPrice(): float
    {
        return match ($this->plan) {
            'Diario' => (float) $this->price_diario,
            'Semanal' => (float) $this->price_semanal,
            'Quincenal' => (float) $this->price_quincenal,
            'Mensual' => (float) $this->price_mensual,
            default => (float) $this->price_mensual,
        };
    }

    /**
     * Get the remaining balance of the financing.
     */
    public function getRemainingBalance(): float
    {
        $totalPagado = $this->payments()
            ->where('status', 'approved')
            ->where('installment_number', '>', 0)
            ->sum('total');

        return round(($this->final_price ?? 0) - $totalPagado, 2);
    }

    /**
     * Check if the financing has a pending balance.
     */
    public function hasPendingBalance(): bool
    {
        return $this->getRemainingBalance() > 0;
    }

    /**
     * Get the price for a specific installment number, handling pro-rata for the first one.
     */
    public function getInstallmentPrice(int $n): float
    {
        $standardPrice = $this->getCurrentInstallmentPrice();

        if ($n === 1) {
            $firstDueDate = $this->getNthDueDate(1)->startOfDay();
            $startDate = \Carbon\Carbon::parse($this->start_date)->startOfDay();
            $daysElapsed = $startDate->diffInDays($firstDueDate);

            $divisor = match ($this->plan) {
                'Semanal' => 7,
                'Quincenal' => 15,
                'Mensual' => 30,
                default => null
            };

            if ($divisor && $daysElapsed < $divisor) {
                return ($standardPrice / $divisor) * $daysElapsed;
            }
        }

        // If it's a pro-rated financing, the last extra installment is the difference
        if ($n > $this->installments) {
            $remaining = $this->getRemainingBalance();
            return $remaining > 0 ? min($standardPrice, $remaining) : 0;
        }

        $remaining = $this->getRemainingBalance();
        return $remaining > 0 ? min($standardPrice, $remaining) : 0;
    }

    /**
     * funcion para el prorrateo de la primera cuota
     */
    public function getTotalInstallmentsCount(): int
    {

        if (!in_array($this->plan, ['Semanal', 'Quincenal', 'Mensual'])) {
            return $this->installments;
        }

        $i1 = $this->getInstallmentPrice(1);
        $standardPrice = $this->getCurrentInstallmentPrice();

        if (round($i1, 2) < round($standardPrice, 2)) {
            return $this->installments + 1;
        }

        return $this->installments;
    }
}
