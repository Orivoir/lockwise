document.addEventListener('DOMContentLoaded' , () => {

    // remove auto label of twig
    ( () => {
        window.__ = document.querySelector('#account_form_codeRecups') ;
        if( window.__ instanceof Node )
            window.__.parentNode.parentNode.removeChild( _.parentNode ) ;
    
        delete window.__;
    } )();

    ( items => {

        [ ...items ]
        .forEach( item => (
            item.addEventListener('click' , function() {

                const
                    id = this.getAttribute( 'data-list-selector' )
                    ,list = document.querySelector( id )
                ;

                if( !(list instanceof Node) ) {
                    
                    console.info('collection type list not found with:' , id );
                    return;
                }

                let count = list.getAttribute( 'data-widget-counter' ) ;
                let inject = list.getAttribute( 'data-prototype' ) ;
                
                inject = inject.replace( /__name__/ig  , count++ ) ;

                list.setAttribute( 'data-widget-counter' , count ) ;

                const 
                    wrap = document.createElement(list.getAttribute('data-widget-tags').split('<').join('').split('>').join('').split('/')[0].trim())
                    ,ref = list.querySelector( wrap.nodeName )
                    ,btnRemove = document.createElement('button')
                ;

                wrap.id = "item-input-code-recup-" + count ;

                btnRemove.classList.add('btn-remove-type')
                btnRemove.setAttribute('type' , "button" ) ;
                btnRemove.innerHTML = '<i class="fas fa-trash-alt"></i>';
                btnRemove.setAttribute('data-item-remove-selector' , "#"+wrap.id )
                btnRemove.addEventListener('click' , function() {

                    const item2remove = document.querySelector( this.getAttribute( 'data-item-remove-selector' ) ) ;
                    item2remove.parentNode.removeChild( item2remove ) ;
                } ) ;

                wrap.innerHTML = inject ;
                wrap.appendChild( btnRemove ) ;
                list[ (ref ? 'insertBefore' : 'appendChild') ]( wrap , ref || undefined ) ;
            } )
        )) ; 
    } )( document.querySelectorAll('.add-another-collection-item') )

    // script only on details account route
    if( /account\/details/i.test(document.location.pathname) ) {

        // script AJAX remove code recup
        ( items => {
            let counter = items.length ;
            let itemCodeRecup = [] ;

            [ ...items ].forEach( item => (
                item.addEventListener('click' , function() {
                    
                    const
                        wrap = document.querySelector(this.getAttribute('data-item-selector'))
                        ,id = wrap.getAttribute('data-item-id')
                        ,slug = wrap.getAttribute('data-item-slug')
                        ,code2remove = wrap.getAttribute('data-item-code')
                        ,target = `/code-recup/remove/${slug}/${id}?code_recup=${code2remove}`
                    ;

                    if( itemCodeRecup.find( i => i == code2remove ) ) {
                        console.info('this code recup load remove ...');
                        return;
                    }

                    if( window.fetch instanceof Function ) {

                        itemCodeRecup.push( code2remove ) ;
                        this.classList.add('btn-load');

                        window.fetch( target , {
                            method: 'GET'
                            ,headers: {
                                'Content-Type': 'application/json'
                            }
                        } ).then( response => (
                            response.json()
                        ) ).then( data => {
                            
                            this.classList.remove('btn-load');
                            if( data.codeId ) {
                                itemCodeRecup = itemCodeRecup.filter( i => i != data.codeId  ) ;
                            }
                            if( data.success ) {

                                document.querySelector('summary b').textContent = --counter ;
                                
                                wrap.classList.add('hide') ;

                            } else {
                                console.log( target );
                                console.info( 'server have reject request for remove code recup with : ' , data  ) ;
                            }

                        } ).catch( error => {

                            console.info('remove code recup request failed with : ' , status );
                            throw `Fetch failed`
                        } );

                    } else {
                        throw "fetch API is not support with this browser";
                    }
    
                } ) 
            ) )

        } )( document.querySelectorAll('.code-recup-list li.code-recup-item button[data-item-selector]') ) ;

        // handler shortcut init
        const handleShortcut = [] ;

        // handlers shortcuts define
        ( () => {

            // handler shortcut methods
            ( (hsh) => {
    
                hsh.match = function( keyName ) {
    
                    this.matchers = this.filter( handle => {
        
        
                        const  {command} = handle ;
        
                        if( command.indexOf('+') !== -1 ) {
        
                            const [leftKey,rightKey] = command.split('+') ;
        
                            const assocKeyName = ( ( ) => (
                                ({
                                    "Control": "ctrl",
                                    "control": "ctrl",
                                    "CONTROL": "ctrl",
                                    'Alt': 'alt',
                                    'ALT': 'alt',
                                    'Shift': 'shift',
                                    'SHIFT': 'shift',
                                    'Backspace': 'back',
                                    'BAcKSPACE': 'back',
                                    'Tab': 'tab',
                                    'TAB': 'tab',
                                })[ keyName ]
                            ) )() ;
        
                            if( assocKeyName ) {
        
                                return (  leftKey === assocKeyName ) ? handle : false;
                            } else if( this.__status  ) {
        
                                return rightKey === keyName ? handle: false;
                            }
        
                            else return false ; // key left is not accept
        
                        } else {
        
                            return false ; // not a valid command
                        }
        
                    } ) ;
                    
                    if( !this.__status ) {
        
                        this.__tempMatchers = this.matchers ;
                        delete this.matchers ;
                        this.on() ;
                    } else {
        
                        this.matchers = this.__tempMatchers.filter( matcher => {
                            return !!this.matchers.find( m => m.name === matcher.name )
                        }  ) ;
        
                        this.matchers.exec = function() {
                                
                            this.forEach( matcher =>
                                matcher.callbacks instanceof Array ? 
                                    matcher.callbacks.forEach( callback =>
                                        callback instanceof Function ? 
                                            callback( matcher.command , matcher.name ) :
                                        null
                                    ) :
                                null
                            ) ;
        
                            hsh.off() ;
                        } ;
                    }
        
                } ;
                
                hsh.on = function() {
        
                    this.__status = true ;
                } ;
                
                hsh.off = function() {
        
                    this.__status = false ;
                    this.__matchers = null;
                } ;
                
                hsh.toggle = function() {
        
                    this.__status = !this.__status ;
        
                    if( !this.__status ) {
                        this.__matchers = null ;
                    }
                } ;
                
                hsh.addPrimary = function( matchers ) {
        
                    hsh.__matchers = !!matchers[0] ? [ matchers ] : [...matchers] ; 
                } ;
    
            } )( handleShortcut ) ;
    
            // keybord event listened with handleShortcut
            ( w => {
    
                w.addEventListener('keydown' , function( e ) {
    
                    // if it's an continous fire
                    if( e.repeat ) return;
    
                    const key = e.key ;
    
                    handleShortcut.match( key ) ;
    
                    const {matchers} = handleShortcut ;
    
                    if( matchers ) {
    
                        matchers.exec() ;
                    }
    
                } ) ;
    
                w.addEventListener('keyup' , () => handleShortcut.off() ) ;
    
            } )( window ) ;
        } )() ;

        // add an new command in handler shortcuts
        handleShortcut.push({
            command: 'ctrl+c',
            name: 'copy',
            callbacks: []
        }) ;

        // get command handler for copy
        const copyHandler = handleShortcut.find( h => h.name === 'copy' ) ;

        if( copyHandler ) {
            // attach copy callback to copy command handler
            copyHandler.callbacks.push( onCopy ) ;
        } 

        // fire on auto copy password
        function onCopy() {

            const
                visiblePass = document.querySelector('[data-pass-visible]')
                ,hidePass = document.querySelector('[data-pass-hide]')
            ;

            hidePass.classList[ window._passStatus ? 'add': 'remove' ]( 'o-hide' );
            visiblePass.classList[ !window._passStatus ? 'add': 'remove' ]( 'o-hide' );

            if( window._passStatus ) {

                const input = visiblePass.querySelector('input');
                input.focus();
                input.select();
                const accept = document.execCommand( 'copy' );

                if( accept ) {
                    setTimeout(() => {
                        document.querySelector('[data-pass-wrap]').click();
                        document.querySelector('.password-copied').classList.remove('o-hide');
                    }, 250);
                }
            }

            window._passStatus = !window._passStatus;
        }

        // event exec close banner auto copy password with success
        document.querySelector('.password-copied button').addEventListener('click' , function() {
            this.parentNode.classList.add('o-hide') ;
        } ) ;

        window._passStatus = true;
        // show/hide password
        ( passwordToggles => (
            [...passwordToggles].map( passToggle => {
                passToggle._status = true;
                return passToggle;
            } ).forEach( passToggle => (
                passToggle.addEventListener('click' , onCopy )
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