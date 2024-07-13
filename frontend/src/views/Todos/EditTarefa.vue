<template>
  <div class="container">
    <h1 class="p-3">Editar Tarefa</h1>
    <form @submit.prevent="editTarefa" class="mx-auto mt-4 container-fluid">
      <div class="form-group row p-1">
        <label for="inputName" class="col-sm-2 col-form-label">Nome</label>
        <div class="col-sm-10">
          <input type="text" class="form-control" id="inputName" placeholder="nome" v-model="tarefa.name">
        </div>
      </div>
      <div class="form-group row p-1">
        <label for="inputAddress" class="col-sm-2 col-form-label">Endereço</label>
        <div class="col-sm-10">
          <input type="text" class="form-control" id="inputAddress" placeholder="endereço" v-model="tarefa.address">
        </div>
      </div>
      <div class="form-group row p-1">
        <label for="inputEmail" class="col-sm-2 col-form-label">Email</label>
        <div class="col-sm-10">
          <input type="email" class="form-control" id="inputEmail" placeholder="email" v-model="tarefa.email">
        </div>
      </div>
      <div class="form-group row p-1">
        <label for="inputTelephone" class="col-sm-2 col-form-label">Telefone</label>
        <div class="col-sm-10">
          <input type="text" class="form-control" id="inputTelephone" placeholder="telefone" v-model="tarefa.telephone">
        </div>
      </div>
      <div class="form-group row p-1">
        <label for="inputImage" class="col-sm-2 col-form-label">Imagem</label>
        <div class="col-sm-10">
          <input type="file" class="form-control-file" id="inputImage" @change="handleImageUpload" />
          <img v-if="tarefa.imageUrl" :src="tarefa.imageUrl" alt="Imagem atual" class="mt-3" style="max-width: 200px;">
          <span v-if="errors.image" class="text-danger">{{ errors.image }}</span>
        </div>
      </div>
      <div class="form-group row">
        <div class="col-sm-12 text-end">
          <button class="btn btn-primary " type="submit">Salvar</button>
        </div>
      </div>
    </form>
  </div>
</template>

<script>
import TodoService from '@/services/todo-services';
import { reactive, onMounted } from 'vue';
import router from '@/router';
import 'bootstrap/dist/css/bootstrap.css';

export default {
  name: 'EditTarefa',
  props: {
    id: {
      required: true
    }
  },
  setup(props) {
    const tarefa = reactive({
      name: '',
      address: '',
      email: '',
      telephone: '',
      image: null,
      imageUrl: ''
    });

    const errors = reactive({});

    const handleImageUpload = (event) => {
      const file = event.target.files[0];
      tarefa.image = file;
    };

    onMounted(async () => {
      try {
        const response = await TodoService.getOne(props.id);
        const data = response.data;
        tarefa.name = data.name;
        tarefa.address = data.address;
        tarefa.email = data.email;
        tarefa.telephone = data.telephone;
        if (data.image) {
          tarefa.imageUrl = `http://localhost:8989/storage/${data.image}`;
        }
      } catch (error) {
        console.error(error);
      }
    });

    const editTarefa = async () => {
      const formData = new FormData();
      formData.append('name', tarefa.name);
      formData.append('address', tarefa.address);
      formData.append('email', tarefa.email);
      formData.append('telephone', tarefa.telephone);
      if (tarefa.image) {
        formData.append('image', tarefa.image);
      }

      try {
        await TodoService.editTarefa(props.id, formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        });
        router.push({ name: 'todo.index' });
      } catch (error) {
        if (error.response && error.response.data) {
          Object.assign(errors, error.response.data.errors);
        }
      }
    };

    return {
      tarefa,
      errors,
      editTarefa,
      handleImageUpload
    };
  }
};
</script>
