{{-- Fields shared by the create and edit tip forms. Expects $tip, which is null when creating. --}}
<x-input-field label="Title" name="title" :value="$tip?->title" maxlength="150" placeholder="e.g. How to get paid faster" required />

<x-textarea-field label="Tip" name="body" rows="10" maxlength="10000" :value="$tip?->body" hint="Plain text. Line breaks are kept." required />

<label class="flex w-fit cursor-pointer items-start gap-3">
    <input type="hidden" name="is_published" value="0">
    <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $tip ? $tip->isPublished() : true)) class="mt-0.5 size-4 rounded border-slate-300 accent-blue-600">
    <span>
        <span class="block text-sm font-medium text-slate-700 dark:text-slate-300">Publish this tip</span>
        <span class="block text-xs text-slate-500 dark:text-slate-400">Unpublished tips are saved as drafts that only admins can see.</span>
    </span>
</label>
