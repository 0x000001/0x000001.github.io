while true; do
     score=$(curl 'https://www.random.org/integers/?num=1&min=3000&max=5200&col=1&base=10&format=plain&rnd=new')
     curl 'http://vagex.com/vfapp.php'  --data "user=407476&score=$score"
    sleep 24h
done