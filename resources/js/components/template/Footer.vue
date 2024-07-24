<template>
    <footer class="container-fluid greenBackground text-center p-5 mt-5">
        <img id="logoFooter" src="images/logo2.png" alt="logo alternatif nice places (footer)">
        <p class="text-white fs-4">sorties nature près de chez vous</p>
        <p><router-link to="/politique">mentions légales / politique de confidentialité</router-link> | 
            <router-link to="/contact">contact</router-link></p>
        <p>© 2022-2023 Nice Places</p>
    </footer>
</template>

<style scoped>
a:hover {
    color: white !important
}

footer p {
    color: #1C6E8C
}

#logoFooter {
    width: 30vw
}

@media screen and (min-width: 769px) and (max-width: 1024px) {
    #logoFooter {
        width: 50vw
    }
}

@media screen and (max-width: 768px) {
    #logoFooter {
        width: 70vw
    }
}
</style>
