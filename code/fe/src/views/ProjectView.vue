<template>
  <div class="grid root">
    <div class="col">
      <Card>
        <template #title>
          Project
        </template>
        <template #content>
          <ProjectList :menu="true"/>
        </template>
      </Card>
    </div>
  </div>
</template>

<script>
// @ is an alias to /src
import ProjectList from '@/components/ProjectListComponent.vue'
import Card from 'primevue/card'

export default {
  name: 'ProjectView',
  components: {
    ProjectList,
    Card
  }
}
</script>

<style scoped>
  .root {
    margin-top: 10px;
  }
</style>
