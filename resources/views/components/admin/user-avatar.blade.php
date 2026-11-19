@props(['user'])

<span {{ $attributes->class([
    'flex shrink-0 items-center justify-center rounded-full bg-linear-to-br from-blue-500 to-indigo-600 font-semibold text-white',
    'size-10 text-sm' => ! str_contains($attributes->get('class', ''), 'size-'),
]) }} aria-hidden="true">{{ $user->initials() }}</span>
