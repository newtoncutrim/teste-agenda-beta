<template>
  <div class="container">
    <h1 class="p-3">Adicionar Tarefa</h1>
    <form
      action="#"
      method="post"
      @submit.prevent="addTarefa"
      class="mx-auto mt-4 container-fluid"
    >
      <div class="form-group row p-1">
        <label for="inputName" class="col-sm-2 col-form-label">Nome</label>
        <div class="col-sm-10">
          <input
            type="text"
            class="form-control"
            id="inputName"
            placeholder="Nome"
            v-model="tarefa.name"
          />
          <span v-if="errors.name" class="text-danger">{{ errors.name }}</span>
        </div>
      </div>
      <div class="form-group row p-1">
        <label for="inputAddress" class="col-sm-2 col-form-label">Endereço</label>
        <div class="col-sm-10">
          <input
            type="text"
            class="form-control"
            id="inputAddress"
            placeholder="Endereço"
            v-model="tarefa.address"
          />
          <span v-if="errors.address" class="text-danger">{{ errors.address }}</span>
        </div>
      </div>
      <div class="form-group row p-1">
        <label for="inputEmail" class="col-sm-2 col-form-label">Email</label>
        <div class="col-sm-10">
          <input
            type="email"
            class="form-control"
            id="inputEmail"
            placeholder="Email"
            v-model="tarefa.email"
          />
          <span v-if="errors.email" class="text-danger">{{ errors.email }}</span>
        </div>
      </div>
      <div class="form-group row p-1">
        <label for="inputTelephone" class="col-sm-2 col-form-label">Telefone</label>
        <div class="col-sm-10">
          <input
            type="text"
            class="form-control"
            id="inputTelephone"
            placeholder="Telefone"
            v-model="tarefa.telephone"
          />
          <span v-if="errors.telephone" class="text-danger">{{ errors.telephone }}</span>
        </div>
      </div>
      <div class="form-group row p-1">
        <label for="inputImage" class="col-sm-2 col-form-label">Imagem</label>
        <div class="col-sm-10">
          <input
            type="file"
            class="form-control-file"
            id="inputImage"
            @change="onFileChange"
          />
          <span v-if="errors.image" class="text-danger">{{ errors.image }}</span>
        </div>
      </div>
      <div class="form-group row">
        <div class="col-sm-12 text-end">
          <button class="btn btn-primary" type="submit">Criar</button>
        </div>
      </div>
    </form>
  </div>
</template>

<script>
import TodoService from "@/services/todo-services";
import { reactive } from "vue";
import router from "@/router";

export default {
  name: "AddTarefa",
  setup() {
    const errors = reactive({});

    const tarefa = reactive({
      name: "",
      address: "",
      email: "",
      telephone: "",
      image: null,
    });

    const addTarefa = async () => {
      const formData = new FormData();
      formData.append("name", tarefa.name);
      formData.append("address", tarefa.address);
      formData.append("email", tarefa.email);
      formData.append("telephone", tarefa.telephone);
      if (tarefa.image) {
        formData.append("image", tarefa.image);
      }

      try {
        await TodoService.addTarefa(formData, {
          headers: {
            "Content-Type": "multipart/form-data",
          },
        });
        router.push({ name: "todo.index" });
      } catch (error) {
        if (error.response && error.response.status === 422) {
          const responseData = error.response.data;
          errors.name = responseData.errors.name ? responseData.errors.name[0] : "";
          errors.address = responseData.errors.address ? responseData.errors.address[0] : "";
          errors.email = responseData.errors.email ? responseData.errors.email[0] : "";
          errors.telephone = responseData.errors.telephone
            ? responseData.errors.telephone[0]
            : "";
          errors.image = responseData.errors.image ? responseData.errors.image[0] : "";
        }
      }
    };

    const onFileChange = (e) => {
      tarefa.image = e.target.files[0];
    };

    return {
      tarefa,
      addTarefa,
      errors,
      onFileChange,
    };
  },
};
</script>
