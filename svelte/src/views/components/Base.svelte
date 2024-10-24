<script>

    import { navigating } from '$app/stores';
    import { page } from '$app/stores';

    import Sidebar from '../components/Sidebar.svelte';
    import Footer from '../components/Footer.svelte';
    import Header from '../components/Header.svelte';

    import { envs } from '../../envs.js';

    import { appData } from '../../stores/appData.js';

</script>

{ #if $appData?.user }
    <!-- Page Wrapper -->
    <div id="wrapper">
        <Sidebar/>

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">
                <Header/>

                <!-- Begin Page Content -->
                <div class="container-fluid">
                    <slot/>
                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <Footer/>

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>
{ /if }

<style>

    :global( html )
    {
        overflow: hidden;
    }

    #content
    {
        position: relative;
    }

    .container-fluid
    {
        padding-top: 10px;
        /*height: 100vh;*/
        width: 100%;
        position: absolute;
        left: 0;
        right: 0;
        top: 70px;
        bottom: 0;
        overflow-y: auto;
    }

</style>