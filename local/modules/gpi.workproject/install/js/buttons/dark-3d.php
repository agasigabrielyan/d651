<div class="l-btns">
    <div class="l-btn">
        <b> &#9998; </b>

        <span></span>
    </div>

    <div class="l-btn">
        <b> &#128465; </b>
        <span></span>
    </div>
</div>



<style>
    body{
        background: #333;
    }

    .l-btns{
        display: flex;
        justify-content: center;
        align-items: center;
        flex-wrap: wrap;
        max-width: 140px;
        gap: 20px;
    }

    .l-btn{
        position: relative;
        width: 60px;
        height: 60px;
        display: flex;
        justify-content: center;
        align-items: center;
        cursor: pointer;
    }

    .l-btn span{
        position: absolute;
        width: 100%;
        height: 100%;
        background: linear-gradient(#555353, #363535, #303030);
        border: 2px solid #222;
        border-right: 6px;
        box-shadow: inset 0 5px 1px rgba(0,0,0,0.35),
        0 5px 5px rgba(0,0,0,0.5),
        0 15px 25px rgba(0,0,0,0.35);
    }

    .l-btn:hover span{
        box-shadow: inset 0 2px 2px rgba(0,0,0,0.35),
        inset 0 5px 5px rgba(0,0,0,0.5),
        inset 0 15px 25px rgba(0,0,0,0.35);
    }

    .l-btn span:before{
        content:'';
        position: absolute;
        inset: 5px 3px;
        border-top: 1px solid #ccc;
        filter: blur(2px);
    }

    .l-btn b{
        position: relative;
        z-index: 10;
        font-size: 1.5em;
        color: #111;
    }
    .l-btn:hover b{
        color: #fff;
        text-shadow: 0 0 5px #219cf3,
        0 0 8px #219cf3;
    }
</style>