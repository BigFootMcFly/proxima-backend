<div>
    <div class="flex items-center gap-3" style="">
        {{ svg(
            name: $icon,
            class: 'fi-color-custom text-custom-500 dark:text-custom-400 fi-color-{$iconColor} h-6 w-6',
            attributes: ['style'=>'--c-400:var(--info-400);--c-500:var(--info-500);']
        ) }}
        {{ $content }}
    </div>
</div>