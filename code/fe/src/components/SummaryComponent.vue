<template>
  <h2>Open Task Summary</h2>
  <div class="grid">
    <div class="col-12 md:col-6">
      <Card>
        <template #title>
          All
        </template>
        <template #content>
          <div class="grid grid-nogutter">
            <div class="col-6">
              <p class="text-base">Total</p>
              <p class="text-sm">{{ data.openTasks.all.total }}</p>
            </div>
            <div class="col-6">
              <p class="text-base">Urgent</p>
              <p class="text-sm">{{ data.openTasks.all.URGENT }}</p>
            </div>
            <div class="col-6">
              <p class="text-base">Normal</p>
              <p class="text-sm">{{ data.openTasks.all.NORMAL }}</p>
            </div>
            <div class="col-6">
              <p class="text-base">Low</p>
              <p class="text-sm">{{ data.openTasks.all.LOW }}</p>
            </div>
          </div>
        </template>
      </Card>
    </div>
    <div class="col-12 md:col-6">
      <Card>
        <template #title>
          Unlimited
        </template>
        <template #content>
          <div class="grid grid-nogutter">
            <div class="col-6">
              <p class="text-base">Total</p>
              <p class="text-sm">{{ data.openTasks.unlimited.total }}</p>
            </div>
            <div class="col-6">
              <p class="text-base">Urgent</p>
              <p class="text-sm">{{ data.openTasks.unlimited.URGENT }}</p>
            </div>
            <div class="col-6">
              <p class="text-base">Normal</p>
              <p class="text-sm">{{ data.openTasks.unlimited.NORMAL }}</p>
            </div>
            <div class="col-6">
              <p class="text-base">Low</p>
              <p class="text-sm">{{ data.openTasks.unlimited.LOW }}</p>
            </div>
          </div>
        </template>
      </Card>
    </div>
    <div class="col-12 md:col-6">
      <Card>
        <template #title>
          Limited
        </template>
        <template #content>
          <div class="grid grid-nogutter">
            <div class="col-6">
              <p class="text-base">Total</p>
              <p class="text-sm">{{ data.openTasks.limited.total }}</p>
            </div>
            <div class="col-6">
              <p class="text-base">Urgent</p>
              <p class="text-sm">{{ data.openTasks.limited.URGENT }}</p>
            </div>
            <div class="col-6">
              <p class="text-base">Normal</p>
              <p class="text-sm">{{ data.openTasks.limited.NORMAL }}</p>
            </div>
            <div class="col-6">
              <p class="text-base">Low</p>
              <p class="text-sm">{{ data.openTasks.limited.LOW }}</p>
            </div>
          </div>
        </template>
      </Card>
    </div>
    <div class="col-12 md:col-6">
      <Card>
        <template #title>
          Expired
        </template>
        <template #content>
          <div class="grid grid-nogutter">
            <div class="col-6">
              <p class="text-base">Total</p>
              <p class="text-sm">{{ data.openTasks.expired.total }}</p>
            </div>
            <div class="col-6">
              <p class="text-base">Urgent</p>
              <p class="text-sm">{{ data.openTasks.expired.URGENT }}</p>
            </div>
            <div class="col-6">
              <p class="text-base">Normal</p>
              <p class="text-sm">{{ data.openTasks.expired.NORMAL }}</p>
            </div>
            <div class="col-6">
              <p class="text-base">Low</p>
              <p class="text-sm">{{ data.openTasks.expired.LOW }}</p>
            </div>
          </div>
        </template>
      </Card>
    </div>
  </div>
  <h2>Open Project Summary</h2>
  <div class="grid">
    <div class="col">
      <Card>
        <template #title>
          All
        </template>
        <template #content>
          <div class="grid grid-nogutter">
            <div class="col-12">
              <p class="text-base">Total</p>
              <p class="text-sm">{{ data.openProject.total }}</p>
            </div>
          </div>
        </template>
      </Card>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import Card from 'primevue/card'
import axios from 'axios'
import { useToast } from 'primevue/usetoast'

export default {
  components: {
    Card
  },
  setup () {
    const toast = useToast()
    const data = ref({
      openTasks: {
        all: {
          total: 0,
          URGENT: 0,
          NORMAL: 0,
          LOW: 0
        },
        unlimited: {
          total: 0,
          URGENT: 0,
          NORMAL: 0,
          LOW: 0
        },
        limited: {
          total: 0,
          URGENT: 0,
          NORMAL: 0,
          LOW: 0
        },
        expired: {
          total: 0,
          URGENT: 0,
          NORMAL: 0,
          LOW: 0
        }
      },
      openProject: {
        total: 5
      }
    })

    const getSummary = () => {
      axios({
        method: 'get',
        url: 'http://' + window.location.hostname + ':8080/api/task/summary',
        headers: {}
      }).then(function (response) {
        const summary = response.data
        let total

        // all
        total = 0
        for (const index in summary.all) {
          data.value.openTasks.all[summary.all[index].level] = summary.all[index].total
          total += summary.all[index].total
          data.value.openTasks.all.total = total
        }

        // unlimited
        total = 0
        for (const index in summary.unlimited) {
          data.value.openTasks.unlimited[summary.unlimited[index].level] = summary.unlimited[index].total
          total += summary.unlimited[index].total
          data.value.openTasks.unlimited.total = total
        }

        // limited
        total = 0
        for (const index in summary.limited) {
          data.value.openTasks.limited[summary.limited[index].level] = summary.limited[index].total
          total += summary.limited[index].total
          data.value.openTasks.limited.total = total
        }

        // expired
        total = 0
        for (const index in summary.expired) {
          data.value.openTasks.expired[summary.expired[index].level] = summary.expired[index].total
          total += summary.expired[index].total
          data.value.openTasks.expired.total = total
        }
      }).catch(() => {
        toast.add({ severity: 'error', summary: 'Error Message', detail: 'Something\'s Wrong', life: 3000 })
      })

      axios({
        method: 'get',
        url: 'http://' + window.location.hostname + ':8080/api/project/summary',
        headers: {}
      }).then(function (response) {
        const summary = response.data
        data.value.openProject.total = summary.all.total
      }).catch(() => {
        toast.add({ severity: 'error', summary: 'Error Message', detail: 'Something\'s Wrong', life: 3000 })
      })
    }

    onMounted(() => {
      getSummary()
    })

    return { data }
  }
}

</script>

<style scoped>
.p-card p {
  line-height: 0.5;
}
</style>
