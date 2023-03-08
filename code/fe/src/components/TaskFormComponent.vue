<template>
  <div class="grid p-fluid">
    <div class="col-12 md:col-6">
      <label>Name</label>
      <div class="p-inputgroup">
        <InputText v-model="formData.name" placeholder="Name" />
      </div>
      <small v-if="validation.name" class="p-error">{{ validation.name }}</small>
    </div>

    <div class="col-12 md:col-6">
      <label>Project</label>
      <div class="p-inputgroup">
        <Dropdown class="min-w-full" v-model="formData.project_id" :options="projectSelections" optionLabel="text" optionValue="value" />
      </div>
      <small v-if="validation.project_id" class="p-error">{{ validation.project_id }}</small>
    </div>

    <div class="col-12 md:col-6">
      <label>Level</label>
      <div class="p-inputgroup">
        <Dropdown class="min-w-full" v-model="formData.level" :options="levelSelections" optionLabel="text" optionValue="value" />
      </div>
      <small v-if="validation.level_id" class="p-error">{{ validation.level_id }}</small>
    </div>

    <div class="col-12 md:col-3">
      <label>Deadline</label>
      <div class="p-inputgroup">
        <Calendar v-model="formData.deadline" dateFormat="yy-mm-dd" :showIcon="true" placeholder="Deadline" />
      </div>
      <small v-if="validation.deadline" class="p-error">{{ validation.deadline }}</small>
    </div>

    <div class="col-12 md:col-3">
      <label>Reminder</label>
      <div class="p-inputgroup">
        <Calendar v-model="formData.reminder" dateFormat="yy-mm-dd" :showIcon="true" placeholder="Reminder" />
      </div>
      <small v-if="validation.reminder" class="p-error">{{ validation.reminder }}</small>
    </div>

    <div class="col-12 md:col-12">
      <label>Desctiption</label>
      <div class="p-inputgroup">
        <Textarea v-model="formData.description" />
      </div>
      <small v-if="validation.description" class="p-error">{{ validation.description }}</small>
    </div>
  </div>
  <div class="grid">
    <div class="col-12 md:col-12 text-right">
      <Button label="Cancel" class="p-button-danger" @click="cancel" />
      &nbsp;
      <Button label="Save" class="p-button-success" @click="save" />
    </div>
  </div>
  <Loader :display="displayLoader" />
</template>

<script>
import { ref, inject, onMounted } from 'vue'
import InputText from 'primevue/inputtext'
import Textarea from 'primevue/textarea'
import Button from 'primevue/button'
import Calendar from 'primevue/calendar'
import Loader from '@/components/LoaderComponent.vue'
import { useConfirm } from 'primevue/useconfirm'
import { useToast } from 'primevue/usetoast'
import axios from 'axios'
import moment from 'moment'
import Dropdown from 'primevue/dropdown'

export default {
  components: {
    InputText,
    Textarea,
    Calendar,
    Button,
    Loader,
    Dropdown
  },
  setup () {
    const toast = useToast()
    const confirm = useConfirm()
    const displayLoader = ref(false)
    const dialogRef = inject('dialogRef')
    const projectSelections = ref([])
    const levelSelections = ref([])
    const type = ref(null)
    const id = ref(null)

    const formData = ref({
      id: '',
      name: '',
      project_id: 0,
      level: 'NORMAL',
      deadline: '',
      reminder: '',
      description: ''
    })

    const validation = ref({
      id: '',
      name: '',
      project_id: '',
      level_id: '',
      deadline: '',
      reminder: '',
      description: ''
    })
    const error = ref('')

    const store = () => {
      confirm.require({
        message: 'Are you sure you want to proceed?',
        header: 'Confirmation',
        icon: 'pi pi-exclamation-triangle',
        accept: () => {
          displayLoader.value = true
          formData.value.deadline = formData.value.deadline ? moment(formData.value.deadline).toISOString(true).split('T')[0] : ''
          formData.value.reminder = formData.value.reminder ? moment(formData.value.reminder).toISOString(true).split('T')[0] : ''
          axios({
            method: 'post',
            url: 'http://' + window.location.hostname + ':8080/api/task/store',
            headers: {},
            data: formData.value
          }).then(function (response) {
            displayLoader.value = false
            toast.add({ severity: 'success', summary: 'Success Message', detail: 'Success', life: 3000 })
            dialogRef.value.close({ status: 'success' })
          }).catch(error => {
            Object.entries(validation.value).forEach(([key, value]) => {
              validation.value[key] = ''
            })

            const errorObject = error.response.data
            Object.entries(errorObject).forEach(([key, value]) => {
              validation.value[key] = value[0]
            })
            toast.add({ severity: 'error', summary: 'Error Message', detail: 'Validation Error', life: 3000 })
            displayLoader.value = false
          })
        }
      })
    }

    const modify = () => {
      confirm.require({
        message: 'Are you sure you want to proceed?',
        header: 'Confirmation',
        icon: 'pi pi-exclamation-triangle',
        accept: () => {
          displayLoader.value = true
          formData.value.deadline = formData.value.deadline ? moment(formData.value.deadline).toISOString(true).split('T')[0] : ''
          formData.value.reminder = formData.value.reminder ? moment(formData.value.reminder).toISOString(true).split('T')[0] : ''
          axios({
            method: 'put',
            url: 'http://' + window.location.hostname + ':8080/api/task/modify',
            headers: {},
            data: formData.value
          }).then(function (response) {
            displayLoader.value = false
            toast.add({ severity: 'success', summary: 'Success Message', detail: 'Success', life: 3000 })
            dialogRef.value.close({ status: 'success' })
          }).catch(error => {
            Object.entries(validation.value).forEach(([key, value]) => {
              validation.value[key] = ''
            })

            const errorObject = error.response.data
            Object.entries(errorObject).forEach(([key, value]) => {
              validation.value[key] = value[0]
            })
            toast.add({ severity: 'error', summary: 'Error Message', detail: 'Validation Error', life: 3000 })
            displayLoader.value = false
          })
        }
      })
    }

    const save = () => {
      if (type.value === 'add') {
        store()
      } else if (type.value === 'edit') {
        modify()
      }
    }

    const cancel = () => {
      dialogRef.value.close({ status: 'cancel' })
    }

    const prepareStore = () => {
      displayLoader.value = true
      axios({
        method: 'get',
        url: 'http://' + window.location.hostname + ':8080/api/task/store',
        headers: {}
      }).then(function (response) {
        const data = response.data

        projectSelections.value.push({ text: '- NONE- ', value: 0 })
        for (const index in data.projects) {
          projectSelections.value.push({ text: data.projects[index].name, value: data.projects[index].id })
        }

        for (const index in data.task_level) {
          levelSelections.value.push({ text: data.task_level[index], value: data.task_level[index] })
        }

        displayLoader.value = false
      }).catch(error => {
        const errorObject = error.response.data
        Object.entries(errorObject).forEach(([key, value]) => {
          validation.value[key] = value[0]
        })
        toast.add({ severity: 'error', summary: 'Error Message', detail: 'Validation Error', life: 3000 })
        displayLoader.value = false
      })
    }

    const prepareModify = () => {
      displayLoader.value = true
      axios({
        method: 'get',
        url: 'http://' + window.location.hostname + ':8080/api/task/modify',
        headers: {},
        params: { id: id.value }
      }).then(function (response) {
        const data = response.data

        formData.value = {
          id: data.task.id,
          name: data.task.name,
          project_id: (data.task.project_id) ? data.task.project_id : 0,
          level: data.task.level,
          deadline: data.task.deadline,
          reminder: data.task.reminder,
          description: data.task.description
        }

        projectSelections.value.push({ text: '- NONE- ', value: 0 })
        for (const index in data.projects) {
          projectSelections.value.push({ text: data.projects[index].name, value: data.projects[index].id })
        }

        for (const index in data.task_level) {
          levelSelections.value.push({ text: data.task_level[index], value: data.task_level[index] })
        }

        displayLoader.value = false
      }).catch(error => {
        const errorObject = error.response.data
        Object.entries(errorObject).forEach(([key, value]) => {
          validation.value[key] = value[0]
        })
        toast.add({ severity: 'error', summary: 'Error Message', detail: 'Validation Error', life: 3000 })
        displayLoader.value = false
      })
    }

    onMounted(() => {
      type.value = dialogRef.value.data.type
      id.value = dialogRef.value.data.id
      if (type.value === 'add') {
        prepareStore()
      } else if (type.value === 'edit') {
        prepareModify()
      }
    })

    return {
      type,
      id,
      projectSelections,
      levelSelections,
      formData,
      validation,
      error,
      displayLoader,
      save,
      cancel
    }
  }
}
</script>

<style scoped>
  label {
    font-weight: bold;
    line-height: 1.3em;
  }
</style>
