<script setup>
import { ref, computed, watch } from 'vue';
import SettingItem from './SettingItem.vue';

const props = defineProps({
    settings: Array,
    groupName: String,
    groupLabel: String,
    translations: Object,
    fieldErrors: {
        type: Object,
        default: () => ({})
    }
});

defineEmits(['updateSetting']);

// Tạo refs cho các SettingItem
const settingRefs = ref({});

// Expose settingRefs to parent
defineExpose({
    settingRefs
});

// Group settings by subgroup (if present)
const groupedSettings = computed(() => {
    const result = {};
    let hasSubgroups = false;
    
    // Check if settings have subgroups
    props.settings.forEach(setting => {
        if (setting.subgroup) {
            hasSubgroups = true;
            if (!result[setting.subgroup]) {
                result[setting.subgroup] = [];
            }
            result[setting.subgroup].push(setting);
        } else {
            if (!result['general']) {
                result['general'] = [];
            }
            result['general'].push(setting);
        }
    });
    
    // If no subgroups found, return single group
    if (!hasSubgroups) {
        return { [props.groupLabel || props.groupName]: props.settings };
    }
    
    return result;
});
</script>

<template>
    <div class="settings-panel transition-all duration-300">
        <h3 class="text-xl font-medium text-gray-800 mb-6 flex items-center">
            <span class="mr-3">{{ groupLabel }}</span>
            <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-700">
                {{ settings.length }} {{ settings.length === 1 ? 'setting' : 'settings' }}
            </span>
        </h3>
        
        <div v-for="(settingsGroup, subgroup) in groupedSettings" :key="subgroup" class="mb-8">
            <!-- Subgroup heading if grouped -->
            <h4 v-if="Object.keys(groupedSettings).length > 1" class="text-lg font-medium text-gray-700 mb-4 pb-2 border-b">
                {{ subgroup }}
            </h4>
            
            <!-- Settings group -->
            <div class="space-y-8 bg-white rounded-lg">
                <SettingItem 
                    v-for="setting in settingsGroup" 
                    :key="setting.id"
                    :setting="setting"
                    :translations="translations"
                    :error="fieldErrors[setting.id]"
                    @update="(value) => $emit('updateSetting', setting.id, value)"
                    :ref="el => { if (el) settingRefs[setting.id] = el }"
                    class="transition-all duration-300 hover:bg-gray-50 rounded-lg"
                />
            </div>
        </div>
        
        <!-- Padding at bottom for mobile -->
        <div class="md:hidden h-20"></div>
    </div>
</template>

<style scoped>
.settings-panel {
    animation: fadeIn 0.5s ease-in-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style> 