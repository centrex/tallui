# Examples

```php
// app/Livewire/UserForm.php
namespace App\Livewire;

use App\Models\User;
use Livewire\Component;

class UserForm extends Component
{
    public string $name      = '';
    public string $email     = '';
    public string $role      = '';
    public string $country   = '';
    public string $bio       = '';
    public string $dob       = '';
    public bool   $active    = true;
    public bool   $newsletter = false;

    protected array $rules = [
        'name'    => 'required|string|max:255',
        'email'   => 'required|email|unique:users,email',
        'role'    => 'required|in:admin,editor,viewer',
        'country' => 'required|exists:countries,id',
        'bio'     => 'nullable|string|max:1000',
        'dob'     => 'nullable|date',
    ];

    public function save(): void
    {
        $this->validate();

        User::create([
            'name'       => $this->name,
            'email'      => $this->email,
            'role'       => $this->role,
            'country_id' => $this->country,
            'bio'        => $this->bio,
            'dob'        => $this->dob,
            'active'     => $this->active,
        ]);

        session()->flash('success', 'User created successfully.');
    }

    public function render()
    {
        return view('livewire.user-form');
    }
}
```

```blade
{{-- resources/views/livewire/user-form.blade.php --}}
<form wire:submit="save" class="space-y-4 max-w-2xl">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <x-tallui-input
            name="name"
            label="Full Name"
            icon="heroicon-o-user"
            :error="$errors->first('name')"
            wire:model="name"
            required
        />

        <x-tallui-input
            name="email"
            label="Email Address"
            type="email"
            icon="heroicon-o-envelope"
            :error="$errors->first('email')"
            wire:model="email"
            required
        />
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <x-tallui-select
            name="role"
            label="Role"
            placeholder="Choose role..."
            :options="['admin' => 'Administrator', 'editor' => 'Editor', 'viewer' => 'Viewer']"
            :error="$errors->first('role')"
            wire:model="role"
            required
        />

        <x-tallui-select
            name="country"
            label="Country"
            placeholder="Search countries..."
            :searchable="true"
            :error="$errors->first('country')"
            wire:model="country"
        />
    </div>

    <x-tallui-date-picker
        name="dob"
        label="Date of Birth"
        :error="$errors->first('dob')"
        wire:model="dob"
    />

    <x-tallui-textarea
        name="bio"
        label="Biography"
        placeholder="Tell us about this user..."
        :rows="4"
        :error="$errors->first('bio')"
        wire:model="bio"
    />

    <div class="flex flex-wrap gap-6">
        <x-tallui-toggle name="active" label="Active account" color="success" wire:model="active" />
        <x-tallui-checkbox name="newsletter" label="Subscribe to newsletter" wire:model="newsletter" />
    </div>

    <div class="flex gap-3 pt-2">
        <x-tallui-button type="submit" label="Create User" icon="heroicon-o-check" spinner="save" />
        <x-tallui-button label="Cancel" link="{{ route('users.index') }}" />
    </div>
</form>
```

---

## Full Dashboard Example

```blade
<div class="space-y-6">

    {{-- KPI row --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
        <livewire:tallui-line-chart title="Revenue" :poll="30000" :dataProvider="\App\Charts\RevenueChart::class" :height="200" />
        <livewire:tallui-bar-chart  title="Orders"  :poll="30000" :dataProvider="\App\Charts\OrdersChart::class"  :height="200" />
        <livewire:tallui-area-chart title="Users"   :poll="60000" :dataProvider="\App\Charts\UsersChart::class"   :height="200" />
        <livewire:tallui-pie-chart  title="Sources" :donut="true" :dataProvider="\App\Charts\TrafficChart::class" :height="200" />
    </div>

    {{-- Data table --}}
    <div class="card bg-base-100 shadow-sm border border-base-200">
        <div class="card-body">
            <h2 class="card-title">Recent Users</h2>
            <livewire:users-table />
        </div>
    </div>

</div>
```

---

← [Back to docs](../README.md)
