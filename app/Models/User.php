<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Course;
use App\Models\Asset;
use App\Models\StudentProfile;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Lesson;
use App\Models\Meeting;
use App\Models\Commission;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_ADMIN = 'admin';
    public const ROLE_TEACHER = 'teacher';
    public const ROLE_STUDENT = 'student';
    public const STORAGE_PLAN_TRIAL = 'trial';
    public const STORAGE_PLAN_20 = 'plan_20';
    public const STORAGE_PLAN_40 = 'plan_40';
    public const STORAGE_PLAN_80 = 'plan_80';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'last_name',
        'email',
        'password',
        'avatar_path',
        'birth_date',
        'phone',
        'address',
        'country',
        'state',
        'city',
        'postal_code',
        'role',
        'status',
        'storage_plan',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'birth_date' => 'date',
        ];
    }

    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    public function assets()
    {
        return $this->hasMany(Asset::class);
    }

    public function students()
    {
        return $this->hasMany(StudentProfile::class, 'teacher_id');
    }

    public function conversations()
    {
        return $this->belongsToMany(Conversation::class, 'conversation_user');
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function enrolledCourses()
    {
        return $this->belongsToMany(Course::class, 'course_user');
    }

    public function completedLessons()
    {
        return $this->belongsToMany(Lesson::class, 'lesson_user')
            ->withPivot('completed_at')
            ->withTimestamps();
    }

    public function hostedMeetings()
    {
        return $this->hasMany(Meeting::class, 'teacher_id');
    }

    public function meetings()
    {
        return $this->belongsToMany(Meeting::class, 'meeting_user')
            ->withTimestamps();
    }

    public function commissions()
    {
        return $this->belongsToMany(Commission::class, 'commission_student', 'student_id', 'commission_id')
            ->withTimestamps();
    }

    public static function storagePlans(): array
    {
        return [
            self::STORAGE_PLAN_TRIAL => ['label' => 'Prueba 1GB', 'limit_gb' => 1],
            self::STORAGE_PLAN_20 => ['label' => 'Plan 20GB', 'limit_gb' => 20],
            self::STORAGE_PLAN_40 => ['label' => 'Plan 40GB', 'limit_gb' => 40],
            self::STORAGE_PLAN_80 => ['label' => 'Plan 80GB', 'limit_gb' => 80],
        ];
    }

    public function storagePlanKey(): string
    {
        $plans = self::storagePlans();
        return array_key_exists($this->storage_plan, $plans) ? $this->storage_plan : self::STORAGE_PLAN_TRIAL;
    }

    public function storagePlanLabel(): string
    {
        $plan = self::storagePlans()[$this->storagePlanKey()];
        return $plan['label'];
    }

    public function storageLimitBytes(): int
    {
        $plan = self::storagePlans()[$this->storagePlanKey()];
        return (int) ($plan['limit_gb'] * 1024 * 1024 * 1024);
    }

    public function storageUsedBytes(): int
    {
        $storageUsedBytes = (int) Asset::where('user_id', $this->id)->sum('size');
        $missingSizeAssets = Asset::where('user_id', $this->id)
            ->where(function ($query) {
                $query->whereNull('size')->orWhere('size', 0);
            })
            ->get(['id', 'path', 'thumb_path']);

        if ($missingSizeAssets->isNotEmpty()) {
            foreach ($missingSizeAssets as $asset) {
                if ($asset->path && Storage::disk('public')->exists($asset->path)) {
                    $storageUsedBytes += (int) Storage::disk('public')->size($asset->path);
                }
                if ($asset->thumb_path && Storage::disk('public')->exists($asset->thumb_path)) {
                    $storageUsedBytes += (int) Storage::disk('public')->size($asset->thumb_path);
                }
            }
        }

        return $storageUsedBytes;
    }
}
