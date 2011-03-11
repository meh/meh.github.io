syntax on
colorscheme darkblood

" Fix keys
if &term =~ "rxvt"
    exec "set <kPageUp>=\<ESC>[5^"
    exec "set <kPageDown>=\<ESC>[6^"
    exec "set <C-Left>=\<ESC>Od"
    exec "set <C-Right>=\<ESC>Oc"
endif

nnoremap <F2> :set invpaste paste?<CR>
set pastetoggle=<F2>

nnoremap <F3> :set invcul cul?<CR>
imap <F3> <C-O>:set invcul cul?<CR>

set mouse=c
set directory=~/.tmp
set noerrorbells
set novisualbell

set fileencodings=utf-8
set encoding=utf-8
set termencoding=utf-8
set guifont=Terminus\ 8

set helplang=en
set history=200
set hlsearch
set incsearch
set sidescroll=1
set nowrap
set listchars=extends:>,precedes:<

set autoindent
set smartindent
set smarttab
set smartcase
set shiftwidth=4
set ts=4
set expandtab

set statusline=%F%m%r%h%w\ [Format:\ %{&ff}]\ [Type:\ %Y]\ [Lines:\ %L\ @\ %p%%\ {%l;%v}]
set laststatus=2

set number
set showmode ruler

set wildmode=longest:full
set wildmenu

" Conditional stuff
autocmd FileType ruby,yaml,javascript,haml,scss,mkd set shiftwidth=2 ts=2

autocmd BufRead,BufNewFile *.rbuild set ft=ruby
autocmd BufRead,BufNewFile *.markdown set ft=markdown
autocmd BufRead,BufNewFile *.yml set ft=yaml
autocmd BufRead,BufNewFile *.asciidoc set ft=asciidoc

autocmd BufRead,BufNewFile valgrind*.log set ft=valgrind

" Commands
command -range=% Share silent <line1>,<line2>write !curl -s -F "sprunge=<-" http://sprunge.us | head -n 1 | tr -d '\r\n ' | xclip
command -nargs=1 Indentation silent set ts=<args> shiftwidth=<args>

" modify selected text using combining diacritics
command! -range -nargs=0 Overline        call s:CombineSelection(<line1>, <line2>, '0305')
command! -range -nargs=0 Underline       call s:CombineSelection(<line1>, <line2>, '0332')
command! -range -nargs=0 DoubleUnderline call s:CombineSelection(<line1>, <line2>, '0333')
command! -range -nargs=0 Strikethrough   call s:CombineSelection(<line1>, <line2>, '0336')

function! s:CombineSelection(line1, line2, cp)
  execute 'let char = "\u'.a:cp.'"'
  execute a:line1.','.a:line2.'s/\%V[^[:cntrl:]]\%V/&'.char.'/ge'
endfunction

" Mappings
nnoremap <C-T> :TlistAddFilesRecursive .<CR>:TlistSessionSave .session<CR>
nnoremap t :TlistToggle<CR>:TlistSessionLoad .session<CR>

map <C-F> :mksession! .vim.session<CR>
imap <C-F> <C-O>:mksession! .vim.session<CR>

imap <C-z> <C-O>u<CR>
map <C-z> u<CR>
imap <C-y> <C-O><C-R><CR>
map <C-y> <C-R><CR>

imap <silent> <C-H> <C-O>h<CR>
imap <silent> <C-K> <C-O>k<CR>
imap <silent> <C-L> <C-O>l<CR>
imap <silent> <C-J> <C-O>j<CR>

map <silent> <PageUp> 1000<C-U>
map <silent> <PageDown> 1000<C-D>
imap <silent> <PageUp> <C-O>1000<C-U>
imap <silent> <PageDown> <C-O>1000<C-D>

map <C-W> <Nop>

" Tabs
map <silent> <C-T> :tabnew<CR>
map <silent> <C-W> :tabclose<CR>
map <silent> <kPageUp> :tabprevious<CR>
map <silent> <kPageDown> :tabnext<CR>
map <silent> <S-H> :tabprevious<CR>
map <silent> <S-L> :tabnext<CR>
imap <silent> <C-T> <C-O>:tabnew<CR>
imap <silent> <C-W> <C-O>:tabclose<CR>
imap <silent> <kPageUp> <C-O>:tabprevious<CR>
imap <silent> <kPageDown> <C-O>:tabnext<CR>

map <silent> <C-1> 1gt<CR>
map <silent> <C-2> 2gt<CR>
map <silent> <C-3> 3gt<CR>
map <silent> <C-4> 4gt<CR>
map <silent> <C-5> 5gt<CR>
map <silent> <C-6> 6gt<CR>
map <silent> <C-7> 7gt<CR>
map <silent> <C-8> 8gt<CR>
map <silent> <C-9> 9gt<CR>

imap <silent> <C-1> <C-O>1gt<CR>
imap <silent> <C-2> <C-O>2gt<CR>
imap <silent> <C-3> <C-O>3gt<CR>
imap <silent> <C-4> <C-O>4gt<CR>
imap <silent> <C-5> <C-O>5gt<CR>
imap <silent> <C-6> <C-O>6gt<CR>
imap <silent> <C-7> <C-O>7gt<CR>
imap <silent> <C-8> <C-O>8gt<CR>
imap <silent> <C-9> <C-O>9gt<CR>

" Windows
map <silent> <C-Left> :wincmd h<CR>
map <silent> <C-Up> :wincmd k<CR>
map <silent> <C-Right> :wincmd l<CR>
map <silent> <C-Down> :wincmd j<CR>
map <silent> <C-H> :wincmd h<CR>
map <silent> <C-K> :wincmd k<CR>
map <silent> <C-L> :wincmd l<CR>
map <silent> <C-J> :wincmd j<CR>

imap <silent> <C-Left> <C-O>:wincmd h<CR>
imap <silent> <C-Up> <C-O>:wincmd k<CR>
imap <silent> <C-Right> <C-O>:wincmd l<CR>
imap <silent> <C-Down> <C-O>:wincmd j<CR>

" Hex editing
" ex command for toggling hex mode - define mapping if desired
command -bar Hexmode call ToggleHex()

" helper function to toggle hex mode
function ToggleHex()
  " hex mode should be considered a read-only operation
  " save values for modified and read-only for restoration later,
  " and clear the read-only flag for now
  let l:modified=&mod
  let l:oldreadonly=&readonly
  let &readonly=0
  let l:oldmodifiable=&modifiable
  let &modifiable=1
  if !exists("b:editHex") || !b:editHex
    " save old options
    let b:oldft=&ft
    let b:oldbin=&bin
    " set new options
    setlocal binary " make sure it overrides any textwidth, etc.
    let &ft="xxd"
    " set status
    let b:editHex=1
    " switch to hex editor
    %!xxd
  else
    " restore old options
    let &ft=b:oldft
    if !b:oldbin
      setlocal nobinary
    endif
    " set status
    let b:editHex=0
    " return to normal editing
    %!xxd -r
  endif
  " restore values for modified and read only state
  let &mod=l:modified
  let &readonly=l:oldreadonly
  let &modifiable=l:oldmodifiable
endfunction

nnoremap <C-G> :Hexmode<CR>
inoremap <C-G> <Esc>:Hexmode<CR>
vnoremap <C-G> :<C-U>Hexmode<CR>
