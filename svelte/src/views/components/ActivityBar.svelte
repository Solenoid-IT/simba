<div class="activity-bar">
    <div class="line"></div>
    <div class="blackscreen"></div>
</div>

<style>

    .activity-bar
    {
        position: fixed;
        left: 0;
        right: 0;
        top: 0;
        bottom: 0;
        z-index: 10000;
    }

    .line
    {
        width: 100%;
        height: 6px;
        position: relative;
        top: 0;
        z-index: 1;
        /*background: linear-gradient(-45deg, #ee7752, #e73c7e, #23a6d5, #23d5ab);*/
        background: linear-gradient(-45deg, #ee7752, #e73c7e, #d5c623, #c023d5, #d5c623, #e73c7e, #ee7752);
        background-size: auto;
        background-size: 400% 400%;

        animation: activity-gradient 5s linear infinite;
    }

    .blackscreen
    {
        position: fixed;
        left: 0;
        right: 0;
        top: 0;
        background-color: #00000099;

        transition: .5s all ease-in-out;
    }

    .blackscreen.show
    {
        bottom: 0;
    }

    @keyframes activity-gradient
    {
        0%
        {
            background-position: 0% 50%;
        }
        100%
        {
            background-position: 100% 50%;
        }
    }

</style>