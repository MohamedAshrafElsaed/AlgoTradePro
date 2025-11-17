<script setup lang="ts">
import { TabsList, type TabsListProps } from 'reka-ui'
import { cn } from '@/lib/utils'

const props = withDefaults(defineProps<TabsListProps & { class?: string }>(), {
    loop: true,
})
</script>

<template>
    <TabsList
        v-bind="props"
        :class="cn(
      'inline-flex items-center justify-center rounded-lg bg-muted p-1 text-muted-foreground',
      props.class,
    )"
    >
        <slot />
    </TabsList>
</template>
