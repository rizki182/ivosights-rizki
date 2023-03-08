<template>
  <div class="grid p-fluid">
    <div class="col-12 md:col-6">
      <b><label>Name</label></b>
      <div class="p-inputgroup">
        {{ task.name }}
      </div>
    </div>

    <div class="col-12 md:col-6">
      <b><label>Project</label></b>
      <div class="p-inputgroup">
        {{ task.project }}
      </div>
    </div>

    <div class="col-12 md:col-6">
      <b><label>Level</label></b>
      <div class="p-inputgroup">
        {{ task.level }}
      </div>
    </div>

    <div class="col-12 md:col-3">
      <b><label>Deadline</label></b>
      <div class="p-inputgroup">
        {{ task.deadline }}
      </div>
    </div>

    <div class="col-12 md:col-3">
      <b><label>Reminder</label></b>
      <div class="p-inputgroup">
        {{ task.reminder }}
      </div>
    </div>

    <div class="col-12 md:col-12">
      <b><label>Desctiption</label></b>
      <div class="p-inputgroup">
        {{ task.description }}
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
    const task = ref({
      id: '',
      name: '',
      project: '',
      level: '',
      status: '',
      deadline: '',
      reminder: '',
      description: ''
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
        url: 'http://' + window.location.hostname + ':8080/api/task/detail',
        headers: {},
        params: { id: id.value }
      }).then(function (response) {
        const data = response.data

        task.value = {
          id: data.task.id,
          name: data.task.name,
          project: (data.task.project_id) ? data.task.project.name : '',
          level: data.task.level,
          status: data.task.status,
          deadline: data.task.deadline,
          reminder: data.task.reminder,
          description: data.task.description
        }

        displayLoader.value = false
      }).catch(() => {
        toast.add({ severity: 'error', summary: 'Error Message', detail: 'Something\'s Wrong', life: 3000 })
        displayLoader.value = false
      })
    })

    return {
      task,
      edit,
      remove
    }
  }
}
</script>
