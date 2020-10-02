<template>
    <div class="hero-head has-background-white">
    <div v-if="loading" class="loading">
  <vue-loaders name="ball-clip-rotate-multiple" color="#205565" scale="1"></vue-loaders>
    </div>
    <div class="hero-head has-background-white demohead">
      <div :class="{ overlay: isOpen }" @click="(isOpen = !isOpen), (isOpenmenu = !isOpenmenu)"></div>

      <nav class="navbar is-primary" role="navigation" aria-label="main navigation">
        <div class="container is-flex">
          <div class="navbar-brand">
            <a class="navbar-item is-hoverless" href="/">
              <img
                src="../../assets/logo2.png"
                width="120px !important"
                height="20"
                class="is-marginless"
              />
            </a>
          </div>


        </div>
      </nav>
    </div>
  </div>
</template>
<script>
import "vue-loaders/dist/vue-loaders.css";
import { mapState, mapActions, mapGetters } from "vuex";

export default {
  data: function() {
    return {
      isOpen: false,
      overlay: false,
      isopensearch: false,
      isOpenmenu: false,
      isOpenmenuremove: false,
      path: ""
    };
  },
  mounted: function() {
    this.fullpath();
  },
  computed: {
    ...mapState({
      loading: state => state.isLoading
      // userState: state => state.user
    })
  },
  methods: {
    fullpath() {
      this.path = this.$route.path;
    },
    logout() {
      this.$buefy.dialog.confirm({
        message: "Are you sure you want to Logout ?",
        onConfirm: () => this.logoutConfirm()
      });
    },
    logoutConfirm() {
      this.$store.dispatch("logout");
      this.$router.push("/login");
    }
  }
};
</script>
<style lang="scss" scoped>
.demohead img {
  width: 120px;
}

</style>
