<template>
  <div class="home">
    <Summary />
    <div class="grid">
      <div class="col-12 md:col-6">
        <Card>
          <template #title>
            Task
          </template>
          <template #content>
            <TabView>
              <TabPanel header="Today">
                <TaskList :menu="false" />
              </TabPanel>
              <TabPanel header="< 7 Days">
                <TaskList :menu="false" />
              </TabPanel>
              <TabPanel header="< 30 Days">
                <TaskList :menu="false" />
              </TabPanel>
              <TabPanel header="Unlimited">
                <TaskList :menu="false" />
              </TabPanel>
              <TabPanel header="Expired">
                <TaskList :menu="false" />
              </TabPanel>
            </TabView>
          </template>
        </Card>
      </div>
      <div class="col-12 md:col-6">
        <Card>
          <template #title>
            Project
          </template>
          <template #content>
            <TabView>
              <TabPanel header="1 Task">
                <ProjectList :menu="false" />
              </TabPanel>
              <TabPanel header="< 5 Task">
                <ProjectList :menu="false" />
              </TabPanel>
              <TabPanel header="< 10 Task">
                <ProjectList :menu="false" />
              </TabPanel>
              <TabPanel header="> 10 Task">
                <ProjectList :menu="false" />
              </TabPanel>
            </TabView>
          </template>
        </Card>
      </div>
    </div>
  </div>
</template>

<script>
// @ is an alias to /src
import TabView from 'primevue/tabview'
import TabPanel from 'primevue/tabpanel'
import Card from 'primevue/card'
import Summary from '@/components/SummaryComponent.vue'
import TaskList from '@/components/TaskListComponent.vue'
import ProjectList from '@/components/ProjectListComponent.vue'

export default {
  name: 'HomeView',
  components: {
    Summary,
    TaskList,
    ProjectList,
    TabView,
    TabPanel,
    Card
  }
}
</script>

<style scoped>
  .clickable {
    text-decoration: underline;
  }
  .clickable.active {
    color: green;
  }
</style>
