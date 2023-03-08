<template>
  <div class="grid">
    <div class="col-12 md:col-6" v-if="menu">
      <p>Task Left: </p>
      <Dropdown class="min-w-full" v-model="selectedTaskLeft" :options="taskLeftSelections" optionLabel="text" optionValue="value" />
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
      <Button icon="pi pi-plus" class="p-button-success" @click="addProject"/>
    </div>

    <ProgressSpinner v-if="displaySpinner"/>
    <div class="col-12 project-list" v-if="!displaySpinner">
      <div class="grid">
        <div class="col-12 border-green-100 border-solid mb-2 border-round-md project-row" :class="[ project.status === 'CLOSED' ? 'bg-green-100' : '', project.status === 'CLOSED' ? 'CLOSED' : '']" v-for="project in project.data" :key="project.id">
          <div class="grid">
            <div class="col-8"><span class="project-detail" @click="projectDetail(project.id)">{{ project.name }}</span></div>
            <div class="col-2 text-right"><Button icon="pi pi-file-edit" class="p-button-sm p-button-warning p-button-rounded p-button-outlined" v-if="project.status === 'OPEN' && menu" @click="editProject(project.id)"/></div>
            <div class="col-2 text-right"><Button icon="pi pi-times" class="p-button-sm p-button-danger p-button-rounded p-button-outlined" v-if="project.status === 'OPEN' && menu" @click="removeProject(project.id)" /></div>
            <div class="col-12">{{ project.total_closed_task }} / {{ project.total_task }} Task Completed</div>
            <div class="col-10"><ProgressBar :value="completion(project.total_closed_task, project.total_task)" :showValue="false" /></div>
            <div class="col-2">{{ completion(project.total_closed_task, project.total_task) }}%</div>
          </div>
        </div>
      </div>

      <div class="col-12 text-center">
        <Button icon="pi pi-chevron-left" class="p-button-sm p-button-info p-button-rounded"  v-if="project.currentPage > 1" @click="changePage(-1)" />
        &nbsp;
        <Button icon="pi pi-chevron-right" class="p-button-sm p-button-info p-button-rounded" v-if="project.currentPage < project.lastPage" @click="changePage(1)" />
      </div>
    </div>

  </div>
  <Loader :display="displayLoader" />
</template>

<script>
import { ref, watch, onMounted } from 'vue'
import ProgressBar from 'primevue/progressbar'
import InputText from 'primevue/inputtext'
import Button from 'primevue/button'
import Dropdown from 'primevue/dropdown'
import { useDialog } from 'primevue/usedialog'
import ProjectForm from '@/components/ProjectFormComponent.vue'
import ProjectDetail from '@/components/ProjectDetailComponent.vue'
import ProgressSpinner from 'primevue/progressspinner'
import Loader from '@/components/LoaderComponent.vue'
import { useConfirm } from 'primevue/useconfirm'
import axios from 'axios'
import { useToast } from 'primevue/usetoast'

export default {
  components: {
    ProgressBar,
    InputText,
    Button,
    ProgressSpinner,
    Loader,
    Dropdown
  },
  props: ['menu'],
  setup (props) {
    const confirm = useConfirm()
    const toast = useToast()
    const displayLoader = ref(false)
    const displaySpinner = ref(false)
    const project = ref(
      {
        currentPage: 1,
        lastPage: 1,
        data: []
      }
    )

    const selectedStatus = ref('ALL')
    const statusSelections = [
      { text: 'All', value: 'ALL' },
      { text: 'Open', value: 'OPEN' },
      { text: 'Closed', value: 'CLOSED' }
    ]
    const selectedTaskLeft = ref('all')
    const taskLeftSelections = [
      { text: 'All', value: 'all' },
      { text: '1', value: '1' },
      { text: '< 5', value: '5' },
      { text: '< 10', value: '10' }
    ]

    const dialog = useDialog()
    const projectDetail = (id) => {
      dialog.open(ProjectDetail, {
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
              editProject(id)
            } else if (params.data.action === 'remove') {
              removeProject(id)
            }
          }
        }
      })
    }

    const addProject = () => {
      dialog.open(ProjectForm, {
        props: {
          header: 'Add Project',
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
              getProjectList()
            }
          }
        }
      })
    }

    const editProject = (id) => {
      dialog.open(ProjectForm, {
        props: {
          header: 'Edit Project',
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
              getProjectList()
            }
          }
        }
      })
    }

    const getProjectList = (type = '') => {
      if (type === 'reset') {
        project.value = {
          currentPage: 1,
          lastPage: 1,
          data: []
        }
      } else {
        project.value.data = []
      }

      displaySpinner.value = true
      axios({
        method: 'get',
        url: 'http://' + window.location.hostname + ':8080/api/project',
        headers: {},
        params: {
          page: project.value.currentPage
        }
      }).then(function (response) {
        project.value.currentPage = response.data.current_page
        project.value.lastPage = response.data.last_page
        for (const index in response.data.data) {
          project.value.data.push(response.data.data[index])
        }
        displaySpinner.value = false
      }).catch(() => {
        toast.add({ severity: 'error', summary: 'Error Message', detail: 'Something\'s Wrong', life: 3000 })
      })
    }

    const removeProject = (id) => {
      confirm.require({
        message: 'Are you sure you want to delete the project?',
        header: 'Confirmation',
        icon: 'pi pi-exclamation-triangle',
        accept: () => {
          displayLoader.value = true
          axios({
            method: 'delete',
            url: 'http://' + window.location.hostname + ':8080/api/project/remove',
            headers: {},
            params: { id: id }
          }).then(function (response) {
            displayLoader.value = false
            toast.add({ severity: 'success', summary: 'Success Message', detail: 'Success', life: 3000 })
            getProjectList()
          }).catch(() => {
            displayLoader.value = false
            toast.add({ severity: 'error', summary: 'Error Message', detail: 'Something\'s Wrong', life: 3000 })
          })
        }
      })
    }

    const completion = (closed, total) => {
      const percentage = (total) ? (closed / total) * 100 : 0
      return percentage
    }

    const changePage = (value) => {
      project.value.currentPage += value
      getProjectList()
    }

    onMounted(() => {
      getProjectList()
    })

    watch(() => props.maxTask, () => {
      getProjectList()
    })

    return {
      displaySpinner,
      displayLoader,
      project,
      selectedStatus,
      statusSelections,
      selectedTaskLeft,
      taskLeftSelections,
      addProject,
      editProject,
      removeProject,
      projectDetail,
      changePage,
      completion
    }
  }
}

</script>

<style scoped>
  .project-detail {
    color: rgb(42, 117, 255);
    cursor: pointer;
    text-decoration: underline;
  }
  .project-row.CLOSED {
    text-decoration: line-through
  }
</style>
