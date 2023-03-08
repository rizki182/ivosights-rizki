<template>
  <div class="grid">
    <div class="col-12 md:col-6" v-if="menu">
      <p>Deadline: </p>
      <Dropdown class="min-w-full" v-model="selectedDeadline" :options="deadlineSelections" optionLabel="text" optionValue="value" />
    </div>
    <div class="col-12 md:col-6" v-if="menu">
      <p>Status: </p>
      <Dropdown class="min-w-full" v-model="selectedStatus" :options="statusSelections" optionLabel="text" optionValue="value" />
    </div>
    <div class="col-12 md:col-6" v-if="menu">
      <div class="p-inputgroup">
        <InputText placeholder="Keyword"/>
        <Button icon="pi pi-search" class="p-button-warning"/>
        &nbsp;
        <Button icon="pi pi-times" class="p-button-danger"/>
      </div>
    </div>
    <div class="col-12 md:col-6 text-right" v-if="menu">
      <Button icon="pi pi-refresh" class="p-button-info"/>
      &nbsp;
      <Button icon="pi pi-plus" class="p-button-success" @click="addTask"/>
    </div>

    <ProgressSpinner v-if="displaySpinner"/>
    <div class="col-12 task-list" v-if="!displaySpinner">
      <div class="grid">
        <div class="col-12 border-green-100 border-solid mb-2 border-round-md task-row" :class="[ task.status === 'CLOSED' ? 'bg-green-100' : '', task.status === 'CLOSED' ? 'CLOSED' : '']" v-for="task in task.data" :key="task.id">
          <div class="grid grid-nogutter">

            <div class="col-2">
              <div class="grid grid-nogutter">
                <div class="col-12"><Button icon="pi pi-check" class="p-button-sm p-button-help p-button-rounded p-button-outlined" v-if="task.status === 'OPEN'" @click="closeTask(task.id)" /></div>
              </div>
            </div>

            <div class="col-4">
              <div class="grid grid-nogutter">
                <div class="col-12 md:col-12 task-name"><span class="task-detail" @click="taskDetail(task.id)">{{ task.name }}</span></div>
                <div class="col-12 md:col-12 task-deadline"><span v-if="task.deadline">{{ task.deadline.substring(0, 10) }}</span></div>
              </div>
            </div>

            <div class="col-4">
              <div class="grid grid-nogutter">
                <div class="col-12 md:col-6"><Tag :class="task.level">{{ task.level }}</Tag></div>
                <div class="col-12 md:col-6"><Tag class="project" v-if="task.project">{{ task.project.name }}</Tag></div>
              </div>
            </div>

            <div class="col-2 text-right">
              <div class="grid grid-nogutter" v-if="task.status === 'OPEN' && menu">
                <div class="col-12 md:col-6"><Button icon="pi pi-file-edit" class="p-button-sm p-button-warning p-button-rounded p-button-outlined" @click="editTask(task.id)"/></div>
                <div class="col-12 md:col-6"><Button icon="pi pi-times" class="p-button-sm p-button-danger p-button-rounded p-button-outlined" @click="removeTask(task.id)" /></div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-12 text-center">
        <Button icon="pi pi-chevron-left" class="p-button-sm p-button-info p-button-rounded"  v-if="task.currentPage > 1" @click="changePage(-1)" />
        &nbsp;
        <Button icon="pi pi-chevron-right" class="p-button-sm p-button-info p-button-rounded" v-if="task.currentPage < task.lastPage" @click="changePage(1)" />
      </div>
    </div>

  </div>
  <Loader :display="displayLoader" />
</template>

<script>
import { ref, watch, onMounted } from 'vue'
import InputText from 'primevue/inputtext'
import Button from 'primevue/button'
import Dropdown from 'primevue/dropdown'
import { useDialog } from 'primevue/usedialog'
import TaskForm from '@/components/TaskFormComponent.vue'
import TaskDetail from '@/components/TaskDetailComponent.vue'
import axios from 'axios'
import { useToast } from 'primevue/usetoast'
import ProgressSpinner from 'primevue/progressspinner'
import Loader from '@/components/LoaderComponent.vue'
import { useConfirm } from 'primevue/useconfirm'
import Tag from 'primevue/tag'

export default {
  components: {
    InputText,
    Button,
    Dropdown,
    ProgressSpinner,
    Loader,
    Tag
  },
  props: ['menu'],
  setup (props) {
    const confirm = useConfirm()
    const toast = useToast()
    const displayLoader = ref(false)
    const displaySpinner = ref(false)
    const selectedStatus = ref('ALL')
    const statusSelections = [
      { text: 'All', value: 'ALL' },
      { text: 'Open', value: 'OPEN' },
      { text: 'Closed', value: 'CLOSED' }
    ]
    const selectedDeadline = ref('all')
    const deadlineSelections = [
      { text: 'All', value: 'all' },
      { text: 'Today', value: 'today' },
      { text: '< 7 Days', value: '7' },
      { text: '< 30 Days', value: '30' },
      { text: 'Unlimited', value: 'unlimited' },
      { text: 'Expired', value: 'expired' }
    ]

    const dialog = useDialog()
    const taskDetail = (id) => {
      dialog.open(TaskDetail, {
        props: {
          header: 'Task',
          modal: true,
          style: {
            width: '70vw'
          }
        },
        data: {
          id: id
        },
        onClose (params) {
          if (params.data) {
            if (params.data.action === 'edit') {
              editTask(id)
            } else if (params.data.action === 'remove') {
              removeTask(id)
            }
          }
        }
      })
    }

    const addTask = () => {
      dialog.open(TaskForm, {
        props: {
          header: 'Add Task',
          modal: true,
          style: {
            width: '70vw'
          }
        },
        data: {
          type: 'add'
        },
        onClose (params) {
          if (params.data) {
            if (params.data.status === 'success') {
              getTaskList()
            }
          }
        }
      })
    }

    const editTask = (id) => {
      dialog.open(TaskForm, {
        props: {
          header: 'Edit Task',
          modal: true,
          style: {
            width: '70vw'
          }
        },
        data: {
          type: 'edit',
          id: id
        },
        onClose (params) {
          if (params.data) {
            if (params.data.status === 'success') {
              getTaskList()
            }
          }
        }
      })
    }

    const removeTask = (id) => {
      confirm.require({
        message: 'Are you sure you want to delete the task?',
        header: 'Confirmation',
        icon: 'pi pi-exclamation-triangle',
        accept: () => {
          displayLoader.value = true
          axios({
            method: 'delete',
            url: 'http://' + window.location.hostname + ':8080/api/task/remove',
            headers: {},
            params: { id: id }
          }).then(function (response) {
            displayLoader.value = false
            toast.add({ severity: 'success', summary: 'Success Message', detail: 'Success', life: 3000 })
            getTaskList()
          }).catch(() => {
            displayLoader.value = false
            toast.add({ severity: 'error', summary: 'Error Message', detail: 'Something\'s Wrong', life: 3000 })
          })
        }
      })
    }

    const closeTask = (id) => {
      confirm.require({
        message: 'Are you sure you want to close the task?',
        header: 'Confirmation',
        icon: 'pi pi-exclamation-triangle',
        accept: () => {
          displayLoader.value = true
          axios({
            method: 'put',
            url: 'http://' + window.location.hostname + ':8080/api/task/close',
            headers: {},
            data: { id: id }
          }).then(function (response) {
            displayLoader.value = false
            toast.add({ severity: 'success', summary: 'Success Message', detail: 'Success', life: 3000 })
            getTaskList()
          }).catch(() => {
            displayLoader.value = false
            toast.add({ severity: 'error', summary: 'Error Message', detail: 'Something\'s Wrong', life: 3000 })
          })
        }
      })
    }

    const task = ref(
      {
        currentPage: 1,
        lastPage: 1,
        data: []
      }
    )

    const changePage = (value) => {
      task.value.currentPage += value
      getTaskList()
    }

    const getTaskList = (type = '') => {
      if (type === 'reset') {
        task.value = {
          currentPage: 1,
          lastPage: 1,
          data: []
        }
      } else {
        task.value.data = []
      }

      displaySpinner.value = true
      axios({
        method: 'get',
        url: 'http://' + window.location.hostname + ':8080/api/task',
        headers: {},
        params: {
          page: task.value.currentPage
        }
      }).then(function (response) {
        task.value.currentPage = response.data.current_page
        task.value.lastPage = response.data.last_page
        for (const index in response.data.data) {
          task.value.data.push(response.data.data[index])
        }
        displaySpinner.value = false
      }).catch(() => {
        toast.add({ severity: 'error', summary: 'Error Message', detail: 'Something\'s Wrong', life: 3000 })
      })
    }

    onMounted(() => {
      getTaskList()
    })

    watch(() => props.type, () => {
      getTaskList()
    })

    return {
      displayLoader,
      displaySpinner,
      selectedStatus,
      statusSelections,
      selectedDeadline,
      deadlineSelections,
      task,
      dialog,
      addTask,
      editTask,
      removeTask,
      closeTask,
      taskDetail,
      changePage
    }
  }
}

</script>

<style scoped>
  .task-detail {
    color: rgb(42, 117, 255);
    cursor: pointer;
    text-decoration: underline;
  }
  .task-row.CLOSED {
    text-decoration: line-through
  }
  .task-name, .task-deadline {
    font-size: 14px;
  }

  .p-tag.project {
    background: rgb(0, 182, 238);
  }

  .p-tag.URGENT {
    background: red;
  }

  .p-tag.NORMAL {
    background: orange;
  }

  .p-tag.LOW {
    background: green;
  }
</style>
