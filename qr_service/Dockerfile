FROM python:3.9-alpine

WORKDIR /app

COPY ./qr_service/requirements.txt .

RUN pip install -r requirements.txt

COPY ./qr_service . 

CMD ["uvicorn", "main:app", "--host", "0.0.0.0", "--port", "8000"]
