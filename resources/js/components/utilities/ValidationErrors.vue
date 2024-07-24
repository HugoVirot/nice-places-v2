<template>
    <div v-if="validationErrors">
        <ul class="alert alert-danger list-unstyled">
            <li v-for="(value, key, index) in validationErrors">{{ value }}</li>
        </ul>
    </div>`
</template>

<script setup>
import { reactive, computed } from 'vue'
defineProps(['errors'])

const validationErrors = computed(() => {
    // on récupère les messages d'erreur à l'intérieur de l'objet errors en props
    let errors = Object.values(errors);
    return errors.flat();
})
</script>
