<template>

    <div class="container-fluid greenBackground text-center py-5 my-5 text-white">
        <h3 class="p-2">Félicitations</h3>
        <i class="fa-solid fa-circle-check fa-5x p-2"></i>
        <h2 class="p-2">{{ message !== 'undefined' ? message : 'succès'}}</h2>
    </div>

</template>

<script>
export default {

    data() {
        return {
            nextPage: "/" + this.$route.params.nextpage,
            message: this.$route.params.message,
            lieuId: this.$route.params.lieuid
        }
    },
    mounted() {
        // selon le choix fait page précédente, on redirige sur page précédente / accueil / autre page
        setTimeout(() => {
            if (this.nextPage == "/lastpage") {
                this.$router.go('-1')
            }
            else if (this.nextPage == "/home") {
                this.$router.push('/')
            }
            else if (this.nextPage == '/uploadimages') {
                let route = '/uploadimages/' + this.lieuId
                this.$router.push(route)
            } else {
                this.$router.push(this.nextPage)
            }
        }, 2500)
    }
}
</script>

<style scoped>
h2 {
    color: white
}
</style>