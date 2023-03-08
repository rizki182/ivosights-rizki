<template>
  <Menu ref="menu" :model="menuItems" :popup="true" />
  <div class="grid">
    <div class="col-12 navbar fixed">
      <div class="grid grid-nogutter">
        <div class="col-6">
          <Button icon="pi pi-align-justify" class="p-button-text p-button-rounded" @click="toggleMenu" />
          <Button icon="pi pi-home" class="p-button-text p-button-rounded" @click="$router.push('/')" />
        </div>
        <div class="col-6 text-right">
        </div>
      </div>
    </div>
  </div>
  <div class="grid ">
    <div class="col-12 content">
      <router-view/>
    </div>
  </div>
  <DynamicDialog />
  <ConfirmDialog />
  <Toast />
</template>

<script>
import { ref } from 'vue'
import Button from 'primevue/button'
import Menu from 'primevue/menu'
import DynamicDialog from 'primevue/dynamicdialog'
import ConfirmDialog from 'primevue/confirmdialog'
import Toast from 'primevue/toast'

export default {
  components: {
    Button,
    Menu,
    DynamicDialog,
    ConfirmDialog,
    Toast

  },
  setup () {
    const visibleLeft = ref(false)
    const menu = ref(null)
    const menuItems = ref([
      {
        label: 'Task',
        to: '/task'
      },
      {
        label: 'Project',
        to: '/project'
      }
    ])

    const toggleMenu = (event) => {
      menu.value.toggle(event)
    }

    return { visibleLeft, menuItems, toggleMenu, menu }
  }
}
</script>

<style scoped>
  .navbar {
    background: white;
  }

  .content {
    background: #f8f9fa;
    margin-top: 50px;
  }

  .separator {
    border: 1px solid rgb(233, 233, 233);
    border-radius: 5px;
  }
</style>
