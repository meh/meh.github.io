" Author: meh.

hi clear

if exists("syntax on")
    syntax reset
endif

let g:colors_name = "darkblood"

set fcs=vert:│,fold:\ 

if &term =~ "rxvt"
  silent !echo -ne "\033]12;\#404040\007"
  let &t_SI = "\033]12;\#b21818\007"
  let &t_EI = "\033]12;\#404040\007"
  autocmd VimLeave * :silent !echo -ne "\033]12;\#b21818\007"
endif

" General colors
hi Normal        term=none      cterm=none      ctermfg=gray      ctermbg=none
hi Directory     term=none      cterm=none      ctermfg=red       ctermbg=none
hi ErrorMsg      term=none      cterm=none      ctermfg=darkred   ctermbg=none
hi NonText       term=bold      cterm=bold      ctermfg=darkgray  ctermbg=none
hi SpecialKey    term=bold      cterm=bold      ctermfg=darkgray  ctermbg=none
hi LineNr        term=none      cterm=none      ctermfg=darkgrey  ctermbg=none
hi IncSearch     term=none      cterm=none      ctermfg=black     ctermbg=darkred
hi Search        term=none      cterm=none      ctermfg=black     ctermbg=darkred
hi Visual        term=none      cterm=none      ctermfg=white     ctermbg=darkred
hi VisualNOS     term=none      cterm=none      ctermfg=white     ctermbg=darkred
hi MoreMsg       term=none      cterm=none      ctermfg=darkgreen ctermbg=none
hi ModeMsg       term=bold      cterm=bold      ctermfg=none      ctermbg=none
hi Question      term=none      cterm=none      ctermfg=darkgreen ctermbg=none
hi WarningMsg    term=none      cterm=none      ctermfg=darkred   ctermbg=none
hi WildMenu      term=none      cterm=none      ctermfg=white     ctermbg=none
hi DiffAdd       term=none      cterm=none      ctermfg=white     ctermbg=darkgreen
hi DiffChange    term=underline cterm=underline ctermfg=none      ctermbg=none
hi DiffDelete    term=none      cterm=none      ctermfg=white     ctermbg=darkred
hi DiffText      term=none      cterm=none      ctermfg=black     ctermbg=darkred
hi StatusLine    term=underline cterm=underline ctermfg=black     ctermbg=darkred
hi StatusLineNC  term=underline cterm=underline ctermfg=white     ctermbg=none
hi VertSplit     term=none      cterm=none      ctermfg=darkred   ctermbg=none
hi CursorColumn  term=none      cterm=none      ctermfg=darkred   ctermbg=none
hi Cursor        term=none      cterm=none      ctermfg=white     ctermbg=darkred
hi Title         term=bold      cterm=bold      ctermfg=white     ctermbg=none
hi Pmenu         term=none      cterm=none      ctermfg=darkred   ctermbg=none
hi PmenuSel      term=none      cterm=none      ctermfg=black     ctermbg=darkred
hi PmenuSbar     term=none      cterm=none      ctermfg=white     ctermbg=darkred
hi Folded        term=none      cterm=none      ctermfg=darkred   ctermbg=none
hi FoldColumn    term=none      cterm=none      ctermfg=darkred   ctermbg=none
hi MatchParen    term=reverse   cterm=reverse   ctermfg=none      ctermbg=none

if &term =~ "linux"
    hi TabLine       term=none cterm=none ctermfg=white ctermbg=none
    hi TabLineSel    term=none cterm=none ctermfg=white ctermbg=darkred
    hi TabLineFill   term=none cterm=none ctermfg=white ctermbg=none
else
    hi TabLine       term=underline cterm=underline ctermfg=white ctermbg=none
    hi TabLineSel    term=underline cterm=underline ctermfg=white ctermbg=darkred
    hi TabLineFill   term=underline cterm=underline ctermfg=white ctermbg=none
endif

" Highlight unwanted spaces
highlight link ExtraWhitespace Search
match ExtraWhitespace /\s\+$/
autocmd BufWinEnter * match ExtraWhitespace /\s\+$/
autocmd InsertEnter * match ExtraWhitespace /\s\+\%#\@<!$/
autocmd InsertLeave * match ExtraWhitespace /\s\+$/
autocmd BufWinLeave * call clearmatches()

if v:version >= 700
    au InsertEnter * hi StatusLine term=none cterm=none ctermfg=white ctermbg=darkred
    au InsertLeave * hi StatusLine term=none cterm=none ctermfg=black ctermbg=darkred
endif

" Taglist
hi TagListFileName term=none cterm=none ctermfg=darkred ctermbg=none

hi Comment     term=bold cterm=bold ctermfg=darkgrey   ctermbg=none
hi PreProc     term=none cterm=none ctermfg=darkgreen  ctermbg=none
hi Constant    term=none cterm=none ctermfg=darkred    ctermbg=none
hi Type        term=none cterm=none ctermfg=darkred    ctermbg=none
hi Statement   term=bold cterm=bold ctermfg=darkyellow ctermbg=none
hi Identifier  term=none cterm=none ctermfg=darkgreen  ctermbg=none
hi Ignore      term=bold cterm=bold ctermfg=darkgray   ctermbg=none
hi Special     term=none cterm=none ctermfg=brown      ctermbg=none
hi Error       term=none cterm=none ctermfg=white      ctermbg=darkred
hi Todo        term=none cterm=none ctermfg=white      ctermbg=darkred
hi Underlined  term=none cterm=none ctermfg=darkred    ctermbg=none
hi Number      term=none cterm=none ctermfg=darkred    ctermbg=none

" Syntax links
hi link String          Constant
hi link Character       Constant
hi link Number          Constant
hi link Boolean         Constant
hi link Float           Number
hi link Function        Identifier
hi link Number          Constant
hi link Repeat          Statement
hi link Label           Statement
hi link Keyword         Statement
hi link Exception       Statement
hi link Operator        Statement
hi link Include         PreProc
hi link Define          PreProc
hi link Macro           PreProc
hi link PreCondit       PreProc
hi link StorageClass    Type
hi link Structure       Type
hi link Typedef         Type
hi link Tag             Special
hi link SpecialChar     Special
hi link Delimiter       Normal
hi link SpecialComment  Special
hi link Debug           Special
hi link Conditional     Statement
