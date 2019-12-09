document.addEventListener('DOMContentLoaded' , () => {

    if( /details/i.test(document.location.pathname) ) {

        document.querySelector('.password-copied button').addEventListener('click' , function() {
            this.parentNode.classList.add('hide') 
        } ) ;

        // script show/hide password only on details account route
        ( passwordToggles => (
            [...passwordToggles].map( passToggle => {
                passToggle._status = true;
                return passToggle;
            } ).forEach( passToggle => (
                passToggle.addEventListener('click' , function() {

                    const
                        hidePass = this.querySelector('.hide-pass')
                        ,visiblePass = this.querySelector('.visible-pass')
                    ;

                    hidePass.classList[ this._status ? 'add': 'remove' ]( 'hide' );
                    visiblePass.classList[ !this._status ? 'add': 'remove' ]( 'hide' );

                    if( this._status ) {

                        const input = visiblePass.querySelector('input');
                        input.focus();
                        input.select();
                        const accept = document.execCommand( 'copy' );

                        if( accept ) {
                            setTimeout(() => {
                                this.click();
                                document.querySelector('.password-copied').classList.remove('hide');
                            }, 250);
                        }
                    }

                    this._status = !this._status;
                } )
            ) )
        ) )( document.querySelectorAll('.password-wrap') )
    }

    const toggleFavorites = document.querySelectorAll('.toggle-favorite');

    let iconsInLoads = [] ;

    [ ...toggleFavorites ]
    .forEach( toggleFavorite => (
        toggleFavorite.addEventListener('click' , function() {

            const
                item = document.querySelector( this.getAttribute('data-item-selector') )
                ,slug = item.getAttribute('data-slug-item')
                ,id = item.getAttribute('data-id-item')
                ,icon = item.querySelector('i.fa-heart')
                ,target = `/account/toggle-favorite/${slug}/${id}`
            ;

            if( iconsInLoads.find( idIcon => idIcon === id ) ) {

                console.info("toggle icon is already in load");
                return;
            }

            if(window.fetch instanceof Function) {

                iconsInLoads.push( id ) ;

                window.fetch( target , {
                    method: 'GET'
                    ,headers: {
                        'Content-Type': 'application/json'
                    }
                } ).then( response => (
                    response.json()
                ) ).then( data => {
                    
                    if( data.success ) {
                        
                        iconsInLoads = iconsInLoads.filter( i => i != data.id ) ;

                        const active = 'fas';
                        const close = 'far';

                        
                        icon.classList.remove( data.status ? close: active ) ;
                        icon.classList.add( data.status ? active: close ) ;
                        
                        if( document.location.pathname === "/" ) {
                            // this route show only favorite item

                            if( !data.status ) {
                                item.classList.add('hide');
                            }
                        }

                    }

                } ).catch( error => {
                    console.error( error );
                    throw "fail fetch";
                } ) ;

            } else {
                throw "fetch API is not support with this browser";
            }

        } )
    ) ) ;

} ) ;