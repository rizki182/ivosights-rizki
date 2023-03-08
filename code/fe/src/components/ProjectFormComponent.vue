<template>
  <div>
    <div class="grid p-fluid">
      <div class="col-12 md:col-12">
        <label>Name</label>
        <div class="p-inputgroup">
          <InputText v-model="formData.name" placeholder="Name" />
        </div>
        <small v-if="validation.name" class="p-error">{{ validation.name }}</small>
      </div>
    </div>
  </div>
  <div class="grid">
    <div class="col-12 md:col-12 text-right">
      <Button label="Cancel" class="p-button-danger" @click="cancel" />
      &nbsp;
      <Button label="Save" class="p-button-success" @click="save" />
    </div>
  </div>
</template>

<script>
import { ref, inject, onMounted } from 'vue'
import InputText from 'primevue/inputtext'
import Button from 'primevue/button'
import { useConfirm } from 'primevue/useconfirm'
import axios from 'axios'
import { useToast } from 'primevue/usetoast'

export default {
  components: {
    InputText,
    Button
  },
  setup () {
    const toast = useToast()
    const confirm = useConfirm()
    const displayLoader = ref(false)
    const dialogRef = inject('dialogRef')
    const type = ref(null)
    const id = ref(null)
    const formData = ref({
      id: '',
      name: ''
    })

    const validation = ref({
      id: '',
      name: ''
    })
    // const error = ref('')

    const store = () => {
      confirm.require({
        message: 'Are you sure you want to proceed?',
        header: 'Confirmation',
        icon: 'pi pi-exclamation-triangle',
        accept: () => {
          displayLoader.value = true
          axios({
            method: 'post',
            url: 'http://' + window.location.hostname + ':8080/api/project/store',
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

    const prepareModify = () => {
      displayLoader.value = true
      axios({
        method: 'get',
        url: 'http://' + window.location.hostname + ':8080/api/project/modify',
        headers: {},
        params: { id: id.value }
      }).then(function (response) {
        const data = response.data

        formData.value = {
          id: data.project.id,
          name: data.project.name
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

    const modify = () => {
      confirm.require({
        message: 'Are you sure you want to proceed?',
        header: 'Confirmation',
        icon: 'pi pi-exclamation-triangle',
        accept: () => {
          displayLoader.value = true
          axios({
            method: 'put',
            url: 'http://' + window.location.hostname + ':8080/api/project/modify',
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

    onMounted(() => {
      type.value = dialogRef.value.data.type
      id.value = dialogRef.value.data.id
      if (type.value === 'edit') {
        prepareModify()
      }
    })

    return {
      formData,
      validation,
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
