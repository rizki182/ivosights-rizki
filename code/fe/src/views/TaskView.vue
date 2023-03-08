<template>
  <div class="grid root">
    <div class="col">
      <Card>
        <template #title>
          Task
        </template>
        <template #content>
          <TaskList :menu="true" />
        </template>
      </Card>
    </div>
  </div>
</template>

<script>
// @ is an alias to /src
import TaskList from '@/components/TaskListComponent.vue'
import Card from 'primevue/card'

export default {
  name: 'TaskView',
  components: {
    TaskList,
    Card
  }
}
</script>

<style scoped>
  .root {
    margin-top: 10px;
  }
</style>
