<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Answer Survey</title>
   <style>
       body { font-family: Arial, sans-serif; }
       .question { margin-bottom: 20px; }
       .choices { margin-top: 10px; }
   </style>
</head>
<body>
   <h1>Answer Survey</h1>
   <form id="answerForm" action="/survey/answer" method="POST">
       @csrf
       <input type="hidden" name="user_id" value="1"> <!-- replace with dynamic user_id -->

       <div id="surveyContainer"></div>
       <button type="submit">Submit Answers</button>
   </form>

   <script>
       async function loadSurvey() {
           const response = await fetch('/survey/get'); // Adjust endpoint as needed
           const survey = await response.json();

           const surveyContainer = document.getElementById('surveyContainer');
           survey.questions.forEach((question, questionIndex) => {
               const questionDiv = document.createElement('div');
               questionDiv.classList.add('question');
               questionDiv.innerHTML = `<h3>${question.content}</h3>`;

               const choicesDiv = document.createElement('div');
               choicesDiv.classList.add('choices');

               question.choices.forEach(choice => {
                   const choiceId = `question_${questionIndex}_choice_${choice.id}`;
                   const choiceInput = document.createElement('input');
                   choiceInput.type = question.type === 'single' ? 'radio' : 'checkbox';
                   choiceInput.name = `answers[${question.id}]${question.type === 'single' ? '' : '[]'}`;
                   choiceInput.value = choice.id;
                   choiceInput.id = choiceId;

                   const choiceLabel = document.createElement('label');
                   choiceLabel.htmlFor = choiceId;
                   choiceLabel.textContent = choice.content;

                   const choiceContainer = document.createElement('div');
                   choiceContainer.appendChild(choiceInput);
                   choiceContainer.appendChild(choiceLabel);

                   choicesDiv.appendChild(choiceContainer);
               });

               questionDiv.appendChild(choicesDiv);
               surveyContainer.appendChild(questionDiv);
           });
       }

       // Load the survey when the page loads
       document.addEventListener('DOMContentLoaded', loadSurvey);
   </script>
</body>
</html>
