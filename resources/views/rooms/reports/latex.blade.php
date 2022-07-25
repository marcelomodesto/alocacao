\documentclass[12pt, portuguese, a4paper, pdftex, fleqn]{article}
\usepackage{adjustbox}
\usepackage[portuguese]{babel}
\usepackage[scaled=.92]{helvet}
\usepackage{fancyhdr}
\usepackage{float}
\usepackage{array}
\usepackage{indentfirst}
\usepackage{multirow}
\usepackage[hidelinks]{hyperref}
\usepackage[svgnames,table]{xcolor}
\usepackage{booktabs, makecell, longtable}
\usepackage[a4paper,inner=1.5cm,outer=1.5cm,top=1cm,bottom=1cm,bindingoffset=0cm]{geometry}
\usepackage{blindtext}
\geometry{textwidth=\paperwidth, textheight=\paperheight, noheadfoot, nomarginpar}

\renewcommand{\familydefault}{\sfdefault}

\pagestyle{fancy}
\fancyhead{}
\renewcommand{\headrulewidth}{0pt}

\begin{document}
\begin{titlepage}

\begin{center}
  
\begin{minipage}{0.3\textwidth}
\begin{figure}[H]
 \includegraphics[scale=0.2]{{!! base_path() . "/logo_ime.jpg" !!}}
\end{figure}
\end{minipage} \hfill
\begin{minipage}{0.2\textwidth}
\begin{figure}[H]
 \includegraphics[scale=0.55]{{!! base_path() . "/logo_usp.jpg" !!}}
\end{figure}
\end{minipage}\\[8cm]

   {\Large \textbf{INSTITUTO DE MATEMATICA E ESTATÍSTICA}}\\[1cm]
   {\large \textbf{Horário das Disciplinas}}\\
   {\large \textbf{{!! $schoolterm->period !!} de {!! $schoolterm->year !!}}}

   \hspace{.45\textwidth} %posiciona a minipage
  \vfill

\vspace{1cm}


\large \textbf{São Paulo}

  \end{center}
\thispagestyle{empty}
\pagebreak
\end{titlepage}

\section*{Bacharelado em Matemática}

\begin{tabular}{ l l }
  Código: & \textbf{45031} \\
  Período: & \textbf{Diurno}
\end{tabular}

\begin{table}[h]
    \begin{center}
        \begin{tabular}{ |c|c|c|c|c|c|c| }
        @php
          $semestres = $schoolterm->period == "1° Semestre" ? [1,3] : [2,4];
        @endphp
        \hline
          Turmas & Horário & Seg & Ter & Qua & Qui & Sex\\
        @foreach($semestres as $semestre)
          @php
            $turmas = App\Models\SchoolClass::whereBelongsTo($schoolterm)
              ->whereHas("courseinformations", function($query)use($semestre){$query->where("numsemidl",$semestre)->where("codcur",45031);})->get();

            $dias = ['seg', 'ter', 'qua', 'qui', 'sex'];  

            $horarios = ["08:00 às 09:40"=>false,
                        "10:00 às 11:40"=>false,
                        "14:00 às 15:40"=>false,
                        "16:00 às 17:40"=>false,
                        "19:20 às 21:00"=>false,
                        "21:10 às 22:50"=>false];
            
            foreach($horarios as $h=>$value){
              foreach($turmas as $turma){
                if($turma->classschedules()->where("horent",explode(" ",$h)[0])->where("horsai",explode(" ",$h)[2])->get()->isNotEmpty()){
                  $horarios[$h] = true;
                }
              }
            }
          @endphp
          \hline
          \multirow{{!! count(array_filter($horarios))*2 !!}}{*}{\makecell{{!! $semestre !!}°\\Semestre}}
          @foreach($horarios as $h=>$show)
            @if($show)
              &\makecell{{!! explode(" ",$h)[0] !!}\\{!! explode(" ",$h)[1] !!}\\{!! explode(" ",$h)[2] !!}} 
              @foreach($dias as $dia)
                &
                \makecell{
                  @foreach($turmas as $turma)
                      @if($turma->classschedules()->where("diasmnocp",$dia)->where("horent",explode(" ",$h)[0])->where("horsai",explode(" ",$h)[2])->get()->isNotEmpty())
                        @if($turma->fusion()->exists())
                          \href{run:https://uspdigital.usp.br/jupiterweb/obterTurma?nomdis=\&sgldis={!! $turma->coddis !!}}{{!! $turma->coddis !!}}\\{!! "T.".substr($turma->codtur,-2,2) ." ". ($turma->fusion->master->room()->exists() ? "S. ".$turma->fusion->master->room->nome : "Sem Sala") !!}\\
                        @else
                          \href{run:https://uspdigital.usp.br/jupiterweb/obterTurma?nomdis=\&sgldis={!! $turma->coddis !!}}{{!! $turma->coddis !!}}\\{!! "T.".substr($turma->codtur,-2,2) ." ". ($turma->room()->exists() ? "S. ".$turma->room->nome : "Sem Sala") !!}\\
                        @endif
                      @endif
                  @endforeach
                }
              @endforeach
              \\ 
              \cline{2-7}
              
            @endif
          @endforeach
          \cline{2-7}
          \hline
        @endforeach
        \end{tabular}
    \end{center}
\end{table}



\end{document}