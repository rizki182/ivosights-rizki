<template>
  <Dialog :modal="true" v-model:visible="test" :closable="false" :draggable="false">
    <ProgressSpinner />
  </Dialog>
</template>

<script>
import { ref, watch } from 'vue'
import Dialog from 'primevue/dialog'
import ProgressSpinner from 'primevue/progressspinner'

export default {
  props: ['display'],
  components: {
    Dialog,
    ProgressSpinner
  },
  setup (props) {
    const test = ref(null)

    watch(() => props.display, (value) => {
      test.value = value
    })

    return { test }
  }
}
</script>
