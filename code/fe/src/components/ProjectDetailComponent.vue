<template>
  <div>
    <div class="grid p-fluid">
      <div class="col-12 md:col-12">
        <b><label>Name</label></b>
        <div class="p-inputgroup">
          {{ project.name }}
        </div>
      </div>
    </div>
  </div>
  <div class="grid">
    <div class="col-12 md:col-12 text-right">
      <Button label="Edit" class="p-button-warning" icon="pi pi-file-edit" @click="edit" />
      &nbsp;
      <Button label="Remove" class="p-button-danger" icon="pi pi-times" @click="remove" />
    </div>
  </div>
</template>

<script>
import { ref, inject, onMounted } from 'vue'
import axios from 'axios'
import { useToast } from 'primevue/usetoast'
import Button from 'primevue/button'

export default {
  components: {
    Button
  },
  setup () {
    const toast = useToast()
    const displayLoader = ref(false)
    const dialogRef = inject('dialogRef')
    const id = ref(null)
    const project = ref({
      id: '',
      name: '',
      status: ''
    })

    const edit = () => {
      dialogRef.value.close({ action: 'edit' })
    }

    const remove = () => {
      dialogRef.value.close({ action: 'remove' })
    }

    onMounted(() => {
      id.value = dialogRef.value.data.id

      axios({
        method: 'get',
        url: 'http://' + window.location.hostname + ':8080/api/project/detail',
        headers: {},
        params: { id: id.value }
      }).then(function (response) {
        const data = response.data

        project.value = {
          id: data.project.id,
          name: data.project.name,
          status: data.project.status
        }

        displayLoader.value = false
      }).catch(() => {
        toast.add({ severity: 'error', summary: 'Error Message', detail: 'Something\'s Wrong', life: 3000 })
        displayLoader.value = false
      })
    })

    return {
      project,
      edit,
      remove
    }
  }
}
</script>
