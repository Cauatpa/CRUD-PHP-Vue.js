const { createApp } = Vue;

createApp({
  data() {
    return {
      title: "Crud - Vue.js, PHP e Bootstrap",
      title_info: "Registro de usuários",
      baseUrl: "../api/users.php",
      msgSucces: "",
      msgError: "",
      msgEmpty: "",
      user: { id: "", name: "", email: "", phone: "" },
      users: [],
    };
  },
  mounted() {
    this.listUser();
  },
  methods: {
    listUser() {
      this.msgEmpty = "";
      this.users = [];

      axios
        .get(`${this.baseUrl}?action=list`)
        .then((response) => {
          if (response.data?.message && !response.data.message.error) {
            this.users = response.data.users || [];
            if (this.users.length === 0) {
              this.msgEmpty = "Nenhum usuário encontrado.";
            }
          } else {
            this.msgEmpty =
              response.data?.message?.msgError || "Nenhum usuário encontrado.";
          }
        })
        .catch(() => {
          this.msgEmpty = "Erro ao buscar usuários.";
        });
    },
    save() {
      const observableUser =
        this.user && this.user.id
          ? this.update(this.user)
          : this.create(this.user);

      observableUser
        .then((response) => {
          if (response.data?.message && !response.data.message.error) {
            this.msgSucces =
              response.data.message.msgSucces || "Usuário salvo com sucesso.";
            this.listUser();
          } else {
            this.msgError =
              response.data?.message?.msgError || "Erro ao salvar usuário.";
          }
        })
        .catch(() => {
          this.msgError = "Erro ao salvar usuário.";
        })
        .finally(() => {
          const el = document.getElementById("userModal");
          const modal =
            bootstrap.Modal.getInstance(el) || new bootstrap.Modal(el);
          modal.hide();

          setTimeout(() => {
            this.msgSucces = "";
            this.msgError = "";
          }, 4000);
        });
    },
    addUser() {
      this.user = { id: "", name: "", email: "", phone: "" };
    },
    create(user) {
      const formData = this.toFormUserData(user);
      formData.append("action", "create");
      return axios.post(this.baseUrl, formData);
    },
    update(user) {
      const formData = this.toFormUserData(user);
      formData.append("action", "update");
      return axios.post(this.baseUrl, formData);
    },
    askDelete(user) {
      if (user) {
        this.user = { ...user };
      }
    },
    confirmDelete() {
      const formData = new FormData();
      formData.append("id", this.user.id);
      formData.append("action", "delete");

      axios.post(this.baseUrl, formData).then((response) => {
        if (response.data?.message && !response.data.message.error) {
          this.msgSucces =
            response.data.message.msgSucces || "Usuário excluído com sucesso.";
          this.listUser();
        } else {
          this.msgError =
            response.data?.message?.msgError || "Erro ao excluir usuário.";
        }
        setTimeout(() => {
          this.msgSucces = "";
          this.msgError = "";
        }, 2000);
      });
    },
    editUser(user) {
      if (user) {
        this.user = { ...user };
        const modal = new bootstrap.Modal(document.getElementById("userModal"));
        modal.show();
      }
    },
    toFormUserData(user) {
      const newUser = new FormData();
      newUser.append("id", user.id || "");
      newUser.append("name", user.name || "");
      newUser.append("email", user.email || "");
      newUser.append("phone", user.phone || "");
      return newUser;
    },
  },
}).mount("#app");
