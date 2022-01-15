<!DOCTYPE html>
<html>
<head>
    <title>Exam Questions</title>
</head>
<body>
    @foreach($questions as $ques)
    <h1><b>{{ $ques['question'] }}?</b></h1>
    1) {{$ques['option1']}}
    2) {{$ques['option2']}}
    3) {{$ques['option3']}}
    4) {{$ques['option4']}}
    <br><br>
    @endforeach
    <br><br>
    <p>Thank you</p>
</body>
</html>