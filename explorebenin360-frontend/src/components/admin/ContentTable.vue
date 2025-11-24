<template>
  <div class="rounded-md border border-black/10 dark:border-white/10 overflow-hidden">
    <table class="min-w-full text-sm">
      <thead class="bg-black/5 dark:bg-white/5">
        <tr>
          <th v-for="col in columns" :key="col.key" class="text-left px-3 py-2">{{ col.label }}</th>
          <th class="text-right px-3 py-2" v-if="showActions"></th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="row in items" :key="row.id" class="border-t border-black/10 dark:border-white/10">
          <td v-for="col in columns" :key="col.key" class="px-3 py-2">
            <slot :name="`cell-${col.key}`" :row="row">{{ row[col.key] }}</slot>
          </td>
          <td v-if="showActions" class="px-3 py-2 text-right">
            <div class="inline-flex gap-2">
              <button v-if="canView" class="btn-base focus-ring h-8 px-3 rounded-md border border-black/10 dark:border-white/10" @click="$emit('view', row)">Voir</button>
              <button v-if="canEdit" class="btn-base focus-ring h-8 px-3 rounded-md border border-black/10 dark:border-white/10" @click="$emit('edit', row)">Éditer</button>
              <button v-if="canDelete" class="btn-base focus-ring h-8 px-3 rounded-md border border-black/10 dark:border-white/10" @click="$emit('delete', row)">Supprimer</button>
            </div>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
<script setup lang="ts">
interface Column { key: string; label: string }
const props = defineProps<{ items: any[]; columns: Column[]; showActions?: boolean; canEdit?: boolean; canDelete?: boolean; canView?: boolean }>()
withDefaults(props, { showActions: true, canEdit: true, canDelete: true, canView: true })
</script>
